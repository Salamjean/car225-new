    // Générer la visualisation des places (Backend side)
    private function generatePlacesVisualization($vehicule, $config, $reservedSeats)
    {
        $placesGauche = $config['placesGauche'];
        $placesDroite = $config['placesDroite'];
        $placesParRanger = $placesGauche + $placesDroite;
        $totalPlaces = (int)$vehicule->nombre_place;
        $nombreRanger = ceil($totalPlaces / $placesParRanger);

        $html = '<div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">';
        
        // En-tête
        $html .= '<div style="display: grid; grid-template-columns: 100px 1fr 80px 1fr; gap: 10px; padding: 15px; background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
            <div style="text-align: center; font-weight: 600; color: #4b5563;">Rangée</div>
            <div style="text-align: center; font-weight: 600; color: #4b5563;">Côté gauche</div>
            <div style="text-align: center; font-weight: 600; color: #4b5563;">Allée</div>
            <div style="text-align: center; font-weight: 600; color: #4b5563;">Côté droit</div>
        </div>';

        $html .= '<div style="max-height: 400px; overflow-y: auto;">';

        $numeroPlace = 1;

        for ($ranger = 1; $ranger <= $nombreRanger; $ranger++) {
            $placesRestantes = $totalPlaces - ($numeroPlace - 1);
            $placesCetteRanger = min($placesParRanger, $placesRestantes);
            $placesGaucheCetteRanger = min($placesGauche, $placesCetteRanger);
            $placesDroiteCetteRanger = min($placesDroite, $placesCetteRanger - $placesGaucheCetteRanger);

            $border = $ranger < $nombreRanger ? 'border-bottom: 1px solid #e5e7eb;' : '';
            $html .= '<div style="display: grid; grid-template-columns: 100px 1fr 80px 1fr; gap: 10px; padding: 20px; align-items: center; ' . $border . '">';
            
            // Numéro de rangée
            $html .= '<div style="text-align: center; font-weight: 600; color: #6b7280;">Rangée ' . $ranger . '</div>';

            // Places côté gauche
            $html .= '<div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">';
            for ($i = 0; $i < $placesGaucheCetteRanger; $i++) {
                $seatNum = $numeroPlace + $i;
                $isReserved = in_array($seatNum, $reservedSeats);
                $bg = $isReserved ? '#9ca3af' : 'linear-gradient(135deg, #e94f1b, #e89116)';
                $cursor = $isReserved ? 'not-allowed' : 'pointer';
                $onclick = $isReserved ? '' : 'onclick="toggleSeatSelection(' . $seatNum . ', this)"';
                $class = $isReserved ? 'seat-unavailable' : 'seat-available'; // Classes pour JS si besoin

                $html .= '<div class="' . $class . '" ' . $onclick . ' id="seat-' . $seatNum . '" style="width: 50px; height: 50px; background: ' . $bg . '; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: ' . $cursor . ';" title="Place ' . $seatNum . '">' . $seatNum . '</div>';
            }
            $html .= '</div>';

            // Allée
            $html .= '<div style="text-align: center;"><div style="width: 10px; height: 40px; background: #9ca3af; border-radius: 5px; margin: 0 auto;"></div></div>';

            // Places côté droit
            $html .= '<div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">';
            for ($i = 0; $i < $placesDroiteCetteRanger; $i++) {
                $seatNum = $numeroPlace + $placesGaucheCetteRanger + $i;
                $isReserved = in_array($seatNum, $reservedSeats);
                $bg = $isReserved ? '#9ca3af' : 'linear-gradient(135deg, #10b981, #059669)';
                $cursor = $isReserved ? 'not-allowed' : 'pointer';
                $onclick = $isReserved ? '' : 'onclick="toggleSeatSelection(' . $seatNum . ', this)"';
                $class = $isReserved ? 'seat-unavailable' : 'seat-available';

                $html .= '<div class="' . $class . '" ' . $onclick . ' id="seat-' . $seatNum . '" style="width: 50px; height: 50px; background: ' . $bg . '; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: ' . $cursor . ';" title="Place ' . $seatNum . '">' . $seatNum . '</div>';
            }
            $html .= '</div>'; // Fin droites
            $html .= '</div>'; // Fin rangée

            $numeroPlace += $placesCetteRanger;
        }

        $html .= '</div></div>'; // Fin container

        // Légende
        $html .= '<div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #e94f1b, #e89116); border-radius: 4px;"></div>
                <span style="color: #4b5563; font-size: 0.9rem;">Disponible (Gauche)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 20px; height: 20px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 4px;"></div>
                <span style="color: #4b5563; font-size: 0.9rem;">Disponible (Droit)</span>
            </div>
             <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 20px; height: 20px; background: #9ca3af; border-radius: 4px;"></div>
                <span style="color: #4b5563; font-size: 0.9rem;">Occupé</span>
            </div>
             <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 20px; height: 20px; background: #3b82f6; border-radius: 4px;"></div>
                <span style="color: #4b5563; font-size: 0.9rem;">Sélectionné</span>
            </div>
        </div>';

        return $html;
    }
