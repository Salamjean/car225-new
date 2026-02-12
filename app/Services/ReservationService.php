<?php

namespace App\Services;

use App\Mail\ReservationCancelledMail;
use App\Models\ProgrammeStatutDate;
use App\Models\Reservation;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\FcmService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ReservationService
{
    /**
     * Calculate refund data based on time remaining before departure.
     * New logic based on hours and fixed penalties.
     */
    public function calculateRefundPercentage(Reservation $reservation): array
    {
        $departureDateTime = $this->getDepartureDateTime($reservation);
        $now = Carbon::now();
        $minutesRemaining = $now->diffInMinutes($departureDateTime, false);

        // Less than 15 minutes: forbidden
        if ($minutesRemaining < 15) {
            return [
                'penalty' => 0, 
                'percentage' => 0, 
                'can_cancel' => false, 
                'time_remaining' => 'Moins de 15 minutes', 
                'minutes_remaining' => $minutesRemaining
            ];
        }

        $hoursRemaining = $minutesRemaining / 60;

        if ($hoursRemaining >= 3) {
            // Free cancellation
            $penalty = 0;
            $percentage = 100;
            $label = 'Plus de 3 heures (Gratuit)';
        } elseif ($hoursRemaining >= 2) {
            // Penalty of 250
            $penalty = 250;
            $percentage = null; // Use penalty instead
            $label = 'Entre 2h et 3h (-250 FCFA)';
        } elseif ($hoursRemaining >= 1) {
            // Penalty of 500
            $penalty = 500;
            $percentage = null;
            $label = 'Entre 1h et 2h (-500 FCFA)';
        } else {
            // Between 15 min and 1 hour
            // Penalty of 500 (assumed same as 1h since not specified otherwise)
            $penalty = 500;
            $percentage = null;
            $label = 'Moins d\'une heure (-500 FCFA)';
        }

        return [
            'penalty' => $penalty,
            'percentage' => $percentage,
            'can_cancel' => true,
            'time_remaining' => $label,
            'minutes_remaining' => $minutesRemaining
        ];
    }

    /**
     * Get refund preview.
     */
    public function getRefundPreview(Reservation $reservation): array
    {
        $refundData = $this->calculateRefundPercentage($reservation);
        $montant = (float) $reservation->montant;
        $isRoundTrip = false;
        $pairedReference = null;

        if ($reservation->is_aller_retour) {
            $pairedReservation = $this->findPairedReservation($reservation);
            if ($pairedReservation) {
                $isRoundTrip = true;
                $montant += (float) $pairedReservation->montant;
                $pairedReference = $pairedReservation->reference;
            }
        }

        // Calculate refund amount
        if ($refundData['percentage'] !== null) {
            $refundAmount = round($montant * $refundData['percentage'] / 100, 0);
        } else {
            $refundAmount = max(0, $montant - $refundData['penalty']);
        }

        return [
            'can_cancel' => $refundData['can_cancel'],
            'penalty' => $refundData['penalty'],
            'percentage' => $refundData['percentage'],
            'montant_original' => $montant,
            'refund_amount' => $refundAmount,
            'time_remaining' => $refundData['time_remaining'],
            'is_round_trip' => $isRoundTrip,
            'reference' => $reservation->reference,
            'paired_reference' => $pairedReference,
            'fee_amount' => $montant - $refundAmount,
            'fee_percentage' => ($montant > 0) ? round(($montant - $refundAmount) / $montant * 100, 1) : 0,
        ];
    }

    /**
     * Cancel a reservation and refund the user's wallet.
     */
    public function cancelReservation(Reservation $reservation, ?string $reason = null): array
    {
        Log::info("Attempting to cancel reservation ID: {$reservation->id}, Status: {$reservation->statut}");

        if ($reservation->statut !== 'confirmee') {
            return ['success' => false, 'message' => "Seules les réservations confirmées peuvent être annulées. (Statut actuel: {$reservation->statut})"];
        }

        $refundData = $this->calculateRefundPercentage($reservation);
        if (!$refundData['can_cancel']) {
            return ['success' => false, 'message' => 'L\'annulation est impossible moins de 15 minutes avant le départ.'];
        }

        try {
            DB::beginTransaction();

            $reservationsToCancel = [$reservation];
            $totalMontant = (float) $reservation->montant;

            if ($reservation->is_aller_retour) {
                $pairedReservation = $this->findPairedReservation($reservation);
                if ($pairedReservation && $pairedReservation->statut === 'confirmee') {
                    $reservationsToCancel[] = $pairedReservation;
                    $totalMontant += (float) $pairedReservation->montant;
                }
            }

            // Calculate refund amount based on new rules
            if ($refundData['percentage'] !== null) {
                $refundAmount = round($totalMontant * $refundData['percentage'] / 100, 0);
            } else {
                $refundAmount = max(0, $totalMontant - $refundData['penalty']);
            }

            foreach ($reservationsToCancel as $res) {
                $res->update([
                    'statut' => 'annulee',
                    'annulation_reason' => $reason ?? 'Annulé par l\'utilisateur',
                    'annulation_date' => now(),
                    'refund_amount' => ($res->id === $reservation->id) ? $refundAmount : 0, // Store total refund on the primary ticket
                    'refund_percentage' => $refundData['percentage'],
                ]);
                $this->freeSeat($res);
            }

            if ($refundAmount > 0) {
                $user = User::findOrFail($reservation->user_id);
                $user->increment('solde', $refundAmount);

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $refundAmount,
                    'type' => 'credit',
                    'description' => "Remboursement annulation " . ($reservation->is_aller_retour ? "aller-retour" : "réservation") . " {$reservation->reference}",
                    'status' => 'completed',
                    'reference' => 'RMB-' . strtoupper(Str::random(10)),
                    'payment_method' => 'wallet'
                ]);
                
                Log::info("User {$user->id} wallet incremented by {$refundAmount}. New balance: {$user->solde}");
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Annulation réussie.',
                'refund_amount' => $refundAmount,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to cancel reservation: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'annulation: ' . $e->getMessage()];
        }
    }

    /**
     * Find the other leg of a round trip.
     */
    public function findPairedReservation(Reservation $reservation): ?Reservation
    {
        if (!$reservation->is_aller_retour) return null;

        // Strategy 1: Same payment_transaction_id (most reliable)
        if ($reservation->payment_transaction_id) {
            $paired = Reservation::where('payment_transaction_id', $reservation->payment_transaction_id)
                ->where('id', '!=', $reservation->id)
                ->where('statut', 'confirmee')
                ->first();
            if ($paired) return $paired;
        }

        // Strategy 2: Same reference (different ID)
        $paired = Reservation::where('reference', $reservation->reference)
            ->where('id', '!=', $reservation->id)
            ->first();
        if ($paired) return $paired;

        // Strategy 3: Reference with/without -RET suffix
        if (str_ends_with($reservation->reference, '-RET')) {
            $baseRef = substr($reservation->reference, 0, -4);
            return Reservation::where('reference', $baseRef)->first();
        } else {
            return Reservation::where('reference', $reservation->reference . '-RET')->first();
        }

        return null;
    }

    /**
     * Modify a reservation (cancel & rebook model).
     */
    public function modifyReservation(Reservation $oldReservation, array $data): array
    {
        if ($oldReservation->statut !== 'confirmee') {
            return ['success' => false, 'message' => 'Seules les réservations confirmées peuvent être modifiées.'];
        }

        $refundData = $this->calculateRefundPercentage($oldReservation);
        if (!$refundData['can_cancel']) {
            return ['success' => false, 'message' => 'Modification impossible moins de 15 minutes avant le départ.'];
        }

        $oldTotal = (float) $oldReservation->montant;
        $reservationsToCancel = [$oldReservation];

        if ($oldReservation->is_aller_retour) {
            $pairedReservation = $this->findPairedReservation($oldReservation);
            if ($pairedReservation) {
                $oldTotal += (float) $pairedReservation->montant;
                $reservationsToCancel[] = $pairedReservation;
            }
        }

        // Calculate residual value based on new rules
        if ($refundData['percentage'] !== null) {
            $residualValue = round($oldTotal * $refundData['percentage'] / 100, 0);
        } else {
            $residualValue = max(0, $oldTotal - $refundData['penalty']);
        }

        $newProgramme = \App\Models\Programme::findOrFail($data['programme_id']);
        $newPrice = (float)str_replace(' ', '', $newProgramme->montant_billet ?? $newProgramme->prix ?? 0);
        $newTotal = $newPrice;

        if ($oldReservation->is_aller_retour && isset($data['return_programme_id'])) {
            $returnProgramme = \App\Models\Programme::findOrFail($data['return_programme_id']);
            $newTotal += (float)str_replace(' ', '', $returnProgramme->montant_billet ?? $returnProgramme->prix ?? 0);
        }

        $difference = $newTotal - $residualValue;

        try {
            DB::beginTransaction();
            $user = User::findOrFail($oldReservation->user_id);

            if ($difference > 0 && (float) $user->solde < $difference) {
                DB::rollBack();
                return ['success' => false, 'message' => "Solde insuffisant.", 'difference' => $difference, 'needs_payment' => true];
            }

            foreach ($reservationsToCancel as $res) {
                $res->update([
                    'statut' => 'annulee',
                    'annulation_reason' => 'Modification',
                    'annulation_date' => now(),
                    'refund_amount' => ($refundData['percentage'] !== null) 
                        ? round($res->montant * $refundData['percentage'] / 100, 0)
                        : max(0, $res->montant - ($refundData['penalty'] / count($reservationsToCancel))), // Distribute penalty
                    'refund_percentage' => $refundData['percentage'],
                ]);
                $this->freeSeat($res);
            }

            // Generate new base reference if needed, or reuse old one
            $newReference = $oldReservation->reference;
            if (!str_contains($newReference, 'MOD-')) {
                $newReference = 'MOD-' . $newReference . '-' . strtoupper(Str::random(4));
            }

            $newReservation = Reservation::create([
                'user_id' => $oldReservation->user_id,
                'programme_id' => $data['programme_id'],
                'compagnie_id' => $newProgramme->compagnie_id,
                'seat_number' => $data['seat_number'],
                'passager_nom' => $oldReservation->passager_nom,
                'passager_prenom' => $oldReservation->passager_prenom,
                'passager_email' => $oldReservation->passager_email,
                'passager_telephone' => $oldReservation->passager_telephone,
                'passager_urgence' => $oldReservation->passager_urgence,
                'date_voyage' => $data['date_voyage'],
                'heure_depart' => $data['heure_depart'],
                'heure_arrive' => $data['heure_arrive'] ?? '',
                'montant' => $newPrice,
                'reference' => $newReference,
                'statut' => 'confirmee',
                'is_aller_retour' => $oldReservation->is_aller_retour,
                'payment_method' => 'wallet',
                'payment_status' => 'payé'
            ]);

            if ($oldReservation->is_aller_retour && isset($data['return_programme_id'])) {
                Reservation::create([
                    'user_id' => $oldReservation->user_id,
                    'programme_id' => $data['return_programme_id'],
                    'compagnie_id' => $returnProgramme->compagnie_id,
                    'seat_number' => $data['return_seat_number'],
                    'passager_nom' => $oldReservation->passager_nom,
                    'passager_prenom' => $oldReservation->passager_prenom,
                    'passager_email' => $oldReservation->passager_email,
                    'passager_telephone' => $oldReservation->passager_telephone,
                    'passager_urgence' => $oldReservation->passager_urgence,
                    'date_voyage' => $data['return_date_voyage'],
                    'heure_depart' => $data['return_heure_depart'],
                    'heure_arrive' => $data['return_heure_arrive'] ?? '',
                    'montant' => $newTotal - $newPrice,
                    'reference' => $newReference . '-RET',
                    'statut' => 'confirmee',
                    'is_aller_retour' => true,
                    'payment_method' => 'wallet',
                    'payment_status' => 'payé'
                ]);
            }

            if ($difference != 0) {
                $user->solde -= $difference;
                $user->save();

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => abs($difference),
                    'type' => $difference > 0 ? 'debit' : 'credit',
                    'description' => "Modification réservation {$oldReservation->reference}",
                    'status' => 'completed',
                    'reference' => 'MOD-' . strtoupper(Str::random(10)),
                    'payment_method' => 'wallet'
                ]);
            }

            DB::commit();
            return ['success' => true, 'message' => 'Modification réussie.', 'new_reservation' => $newReservation];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Modification error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur technique: ' . $e->getMessage()];
        }
    }

    private function getDepartureDateTime(Reservation $reservation): Carbon
    {
        $date = Carbon::parse($reservation->date_voyage)->format('Y-m-d');
        $time = $reservation->heure_depart ?? optional($reservation->programme)->heure_depart ?? '00:00';
        return Carbon::parse("{$date} {$time}");
    }

    private function freeSeat(Reservation $reservation): void
    {
        $statutDate = ProgrammeStatutDate::where('programme_id', $reservation->programme_id)
            ->where('date_voyage', Carbon::parse($reservation->date_voyage)->format('Y-m-d'))
            ->first();
            
        if ($statutDate) {
            // Correct column names: nbre_siege_occupe, staut_place
            $statutDate->decrement('nbre_siege_occupe');
            
            // Re-calculate status based on InitializeProgrammeStatuts logic
            $totalReservedSeats = $statutDate->nbre_siege_occupe;
            $totalPlaces = optional(optional($reservation->programme)->vehicule)->nombre_place ?? 50;
            $percentage = ($totalReservedSeats / max($totalPlaces, 1)) * 100;

            if ($percentage >= 100) {
                $status = 'rempli';
            } elseif ($percentage >= 80) {
                $status = 'presque_complet';
            } else {
                $status = 'vide';
            }
            
            $statutDate->update(['staut_place' => $status]);
        }
    }
}
