<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteDeactivatedUsers implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cutoffDate = now()->subDays(30);

        // Trouver les utilisateurs désactivés depuis plus de 30 jours
        $usersToDelete = \App\Models\User::where('is_active', false)
            ->where('deactivated_at', '<=', $cutoffDate)
            ->get();

        foreach ($usersToDelete as $user) {
            // Supprimer l'utilisateur (SoftDelete ou HardDelete selon la config du modèle, ici Hard)
            // On pourrait vouloir sauvegarder certaines données avant, mais la demande est "supprimé définitivement"
            $user->delete();
        }
    }
}

