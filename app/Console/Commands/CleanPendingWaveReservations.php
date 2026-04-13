<?php

namespace App\Console\Commands;

use App\Models\Paiement;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanPendingWaveReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:clean-pending-wave
                            {--minutes=20 : Délai en minutes avant qu\'une réservation en_attente soit considérée expirée}
                            {--dry-run : Afficher les réservations à annuler sans les annuler}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Annule les réservations Wave en_attente qui n\'ont pas été payées après le délai imparti (libère les sièges)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $isDryRun = $this->option('dry-run');
        $threshold = Carbon::now()->subMinutes($minutes);

        $this->info("🔍 Recherche des réservations Wave en_attente créées avant {$threshold->toDateTimeString()} ({$minutes} min)...");

        // Trouver le payment_method 'wave' ou identifier par la présence d'un payment_transaction_id
        // On cible les réservations en_attente dont le paiement associé est de type Wave et encore pending
        $reservations = Reservation::where('statut', 'en_attente')
            ->where('created_at', '<', $threshold)
            ->whereHas('paiement', function($q) {
                $q->where('payment_method', 'wave')
                  ->where('status', 'pending');
            })
            ->with('paiement')
            ->get();

        // Fallback: réservations sans relation paiement mais avec un payment_transaction_id Wave
        // (certains projets utilisent payment_transaction_id directement)
        $reservationsDirect = Reservation::where('statut', 'en_attente')
            ->where('created_at', '<', $threshold)
            ->whereNotNull('payment_transaction_id')
            ->whereDoesntHave('paiement', function($q) {
                $q->whereIn('status', ['completed', 'paid']);
            })
            ->whereDoesntHave('paiement', function($q) {
                $q->where('payment_method', 'cinetpay');
            })
            ->get();

        // Fusionner en évitant les doublons
        $allReservations = $reservations->merge($reservationsDirect)->unique('id');

        if ($allReservations->isEmpty()) {
            $this->info('✅ Aucune réservation Wave expirée trouvée. Tout est propre.');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  {$allReservations->count()} réservation(s) Wave expirée(s) trouvée(s) :");

        $table = $allReservations->map(fn($r) => [
            'ID'             => $r->id,
            'Transaction'    => $r->payment_transaction_id ?? '—',
            'User'           => $r->user_id,
            'Programme'      => $r->programme_id,
            'Siège'          => $r->numero_siege ?? '—',
            'Créée le'       => $r->created_at->format('d/m/Y H:i'),
            'Âge (min)'      => (int) $r->created_at->diffInMinutes(now()),
        ])->toArray();

        $this->table(
            ['ID', 'Transaction', 'User', 'Programme', 'Siège', 'Créée le', 'Âge (min)'],
            $table
        );

        if ($isDryRun) {
            $this->info('🔎 Mode dry-run : aucune modification effectuée.');
            return Command::SUCCESS;
        }

        $cancelled = 0;
        foreach ($allReservations as $reservation) {
            try {
                $reservation->update([
                    'statut'            => 'annulee',
                    'annulation_reason' => 'Paiement Wave non finalisé (expiration automatique après ' . $minutes . ' min)',
                    'annulation_date'   => now(),
                ]);

                // Annuler le paiement associé si encore pending
                if ($reservation->payment_transaction_id) {
                    Paiement::where('transaction_id', $reservation->payment_transaction_id)
                        ->where('status', 'pending')
                        ->update(['status' => 'cancelled']);
                }

                $cancelled++;
                Log::info('CleanPendingWave: réservation expirée annulée', [
                    'reservation_id' => $reservation->id,
                    'transaction_id' => $reservation->payment_transaction_id,
                    'user_id'        => $reservation->user_id,
                    'age_minutes'    => (int) $reservation->created_at->diffInMinutes(now()),
                ]);
            } catch (\Exception $e) {
                Log::error('CleanPendingWave: erreur annulation réservation #' . $reservation->id, [
                    'error' => $e->getMessage(),
                ]);
                $this->error("Erreur pour la réservation #{$reservation->id} : {$e->getMessage()}");
            }
        }

        $this->info("✅ {$cancelled} réservation(s) Wave annulée(s) avec succès. Sièges libérés.");

        Log::info('CleanPendingWaveReservations terminé', [
            'reservations_trouvees'  => $allReservations->count(),
            'reservations_annulees'  => $cancelled,
            'seuil_minutes'          => $minutes,
        ]);

        return Command::SUCCESS;
    }
}
