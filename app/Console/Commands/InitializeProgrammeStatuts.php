<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Programme;
use App\Models\Reservation;
use App\Models\ProgrammeStatutDate;

class InitializeProgrammeStatuts extends Command
{
    protected $signature = 'programme:init-statuts';
    protected $description = 'Initialiser les statuts par date pour les programmes récurrents';

    public function handle()
    {
        $this->info('Début de l\'initialisation des statuts...');

        // Traiter les programmes récurrents
        $recurrentProgrammes = Programme::where('type_programmation', 'recurrent')->get();
        
        foreach ($recurrentProgrammes as $programme) {
            $this->info("Traitement du programme {$programme->id}...");
            
            // Récupérer toutes les dates uniques de réservation pour ce programme
            $dates = Reservation::where('programme_id', $programme->id)
                ->where('statut', '!=', 'annulee')
                ->distinct()
                ->pluck('date_voyage');
            
            foreach ($dates as $date) {
                $this->updateStatutForDate($programme, $date);
            }
            
            $this->info("  {$dates->count()} dates traitées pour le programme {$programme->id}");
        }

        // Pour les programmes ponctuels, mettre à jour le statut global
        $ponctuelProgrammes = Programme::where('type_programmation', 'ponctuel')->get();
        
        foreach ($ponctuelProgrammes as $programme) {
            $this->updateGlobalStatut($programme);
        }

        $this->info('Initialisation terminée !');
        return 0;
    }

    private function updateStatutForDate($programme, $date)
    {
        $totalReservedSeats = Reservation::where('programme_id', $programme->id)
            ->where('date_voyage', $date)
            ->where('statut', '!=', 'annulee')
            ->sum('nombre_places');

        $totalPlaces = $programme->vehicule->nombre_place ?? 50;
        $percentage = ($totalReservedSeats / max($totalPlaces, 1)) * 100;

        if ($percentage >= 100) {
            $status = 'rempli';
        } elseif ($percentage >= 80) {
            $status = 'presque_complet';
        } else {
            $status = 'vide';
        }

        ProgrammeStatutDate::updateOrCreate(
            [
                'programme_id' => $programme->id,
                'date_voyage' => $date
            ],
            [
                'nbre_siege_occupe' => $totalReservedSeats,
                'staut_place' => $status
            ]
        );
    }

    private function updateGlobalStatut($programme)
    {
        $totalReservedSeats = Reservation::where('programme_id', $programme->id)
            ->where('statut', '!=', 'annulee')
            ->sum('nombre_places');

        $totalPlaces = $programme->vehicule->nombre_place ?? 50;
        $percentage = ($totalReservedSeats / max($totalPlaces, 1)) * 100;

        if ($percentage >= 100) {
            $status = 'rempli';
        } elseif ($percentage >= 80) {
            $status = 'presque_complet';
        } else {
            $status = 'vide';
        }

        $programme->update([
            'nbre_siege_occupe' => $totalReservedSeats,
            'staut_place' => $status
        ]);
    }
}