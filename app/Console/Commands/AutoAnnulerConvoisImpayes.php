<?php

namespace App\Console\Commands;

use App\Models\Convoi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoAnnulerConvoisImpayes extends Command
{
    protected $signature = 'convoi:auto-annuler
                            {--dry-run : Afficher les convois à annuler sans les annuler}';

    protected $description = 'Annule automatiquement les convois confirmés (statut=confirme) dont la date/heure de départ est dépassée et dont le client ne s\'est pas présenté pour payer.';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $now      = Carbon::now();

        $this->info("🕐 [{$now->toDateTimeString()}] Vérification des convois confirmés non payés...");

        /*
         * On cible les convois :
         *   - statut = confirme  (client a accepté mais n'est pas venu payer)
         *   - date/heure de départ dépassée (Carbon compare date + heure)
         */
        $convois = Convoi::where('statut', 'confirme')
            ->whereNotNull('date_depart')
            ->with(['user', 'gare', 'itineraire'])
            ->get()
            ->filter(function (Convoi $convoi) use ($now) {
                // Construire le datetime de départ (date + heure si disponible)
                $heure       = $convoi->heure_depart ?? '00:00:00';
                $dateDepart  = Carbon::parse($convoi->date_depart . ' ' . $heure);
                // Annuler si l'heure de départ est passée
                return $dateDepart->isPast();
            });

        if ($convois->isEmpty()) {
            $this->info('✅ Aucun convoi à annuler. Tout est à jour.');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  {$convois->count()} convoi(s) à annuler :");

        $rows = $convois->map(fn(Convoi $c) => [
            'ID'        => $c->id,
            'Réf'       => $c->reference,
            'Client'    => $c->demandeur_nom ?? '—',
            'Départ'    => $c->date_depart . ' ' . ($c->heure_depart ?? '00:00'),
            'Montant'   => number_format($c->montant, 0, ',', ' ') . ' FCFA',
        ])->values()->toArray();

        $this->table(['ID', 'Référence', 'Client', 'Date départ', 'Montant'], $rows);

        if ($isDryRun) {
            $this->info('🔎 Mode dry-run : aucune modification effectuée.');
            return Command::SUCCESS;
        }

        $annules = 0;

        foreach ($convois as $convoi) {
            try {
                $convoi->update([
                    'statut'      => 'annule',
                    'motif_refus' => 'Annulation automatique — client non présenté à la gare pour paiement avant le départ.',
                ]);

                // ── Notifier le client ──────────────────────────────────────
                $user = $convoi->user;
                if ($user) {
                    $prenom     = $user->prenom ?? $user->name;
                    $depart     = $convoi->lieu_depart  ?? ($convoi->itineraire->point_depart  ?? 'N/A');
                    $arrivee    = $convoi->lieu_retour   ?? ($convoi->itineraire->point_arrive  ?? 'N/A');
                    $dateDepart = Carbon::parse($convoi->date_depart)->format('d/m/Y');
                    $montantF   = number_format($convoi->montant, 0, ',', ' ');

                    // SMS
                    try {
                        app(\App\Services\SmsService::class)->sendSms(
                            $user->contact,
                            "Bonjour {$prenom},\n"
                            . "Votre convoi CAR225 ref {$convoi->reference} ({$depart} → {$arrivee}) a ete ANNULE automatiquement.\n"
                            . "Motif : Non-presentation a la gare avant le {$dateDepart} pour regler les {$montantF} FCFA.\n"
                            . "Contactez-nous pour toute question."
                        );
                    } catch (\Exception $e) {
                        Log::error("AutoAnnuler SMS convoi #{$convoi->id}: " . $e->getMessage());
                    }

                    // FCM
                    try {
                        if ($user->fcm_token) {
                            app(\App\Services\FcmService::class)->sendNotification(
                                $user->fcm_token,
                                'Convoi annulé automatiquement',
                                "Ref {$convoi->reference} · {$depart} → {$arrivee} · Paiement non effectué avant le départ.",
                                ['type' => 'convoi_annule', 'convoi_id' => (string) $convoi->id]
                            );
                        }
                    } catch (\Exception $e) {
                        Log::error("AutoAnnuler FCM convoi #{$convoi->id}: " . $e->getMessage());
                    }

                    // Notification DB
                    try {
                        $user->notify(new \App\Notifications\ConvoiValidatedNotification($convoi));
                    } catch (\Exception $e) {
                        // Silently fail — la notif DB n'est pas critique
                    }
                }

                $annules++;

                Log::info('AutoAnnulerConvois: convoi annulé', [
                    'convoi_id'  => $convoi->id,
                    'reference'  => $convoi->reference,
                    'user_id'    => $convoi->user_id,
                    'date_depart'=> $convoi->date_depart,
                    'heure_depart' => $convoi->heure_depart,
                ]);

                $this->line("  ✓ #{$convoi->id} {$convoi->reference} annulé — client notifié.");

            } catch (\Exception $e) {
                Log::error("AutoAnnulerConvois: erreur pour convoi #{$convoi->id}", [
                    'error' => $e->getMessage(),
                ]);
                $this->error("  ✗ Erreur convoi #{$convoi->id} : {$e->getMessage()}");
            }
        }

        $this->info("✅ {$annules} convoi(s) annulé(s) avec succès.");

        Log::info('AutoAnnulerConvois terminé', [
            'trouves'  => $convois->count(),
            'annules'  => $annules,
            'run_at'   => $now->toDateTimeString(),
        ]);

        return Command::SUCCESS;
    }
}
