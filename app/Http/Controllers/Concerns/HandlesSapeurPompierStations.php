<?php

namespace App\Http\Controllers\Concerns;

use App\Models\SapeurPompier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Logique partagée pour la création et la recherche de casernes
 * (sapeurs-pompiers). Utilisée par :
 *  - {@see \App\Http\Controllers\Admin\SapeurPompierController}
 *  - {@see \App\Http\Controllers\Onpc\OnpcSapeurPompierController}
 *
 * Fournit :
 *  - `searchStations(Request)` : endpoint JSON consommé par le combobox
 *    autocomplete (liste pré-curée + recherche live OpenStreetMap).
 *  - `findDuplicateByCoords()` : détecte une caserne existante autour des
 *    coordonnées soumises (~55m), pour empêcher les doublons.
 */
trait HandlesSapeurPompierStations
{
    /**
     * API JSON consommée par le combobox du formulaire de création.
     * Combine la liste pré-curée locale (config/sapeur_pompiers_ci.php)
     * et une recherche live OpenStreetMap (Nominatim).
     */
    public function searchStations(Request $request): JsonResponse
    {
        $q       = trim((string) $request->input('q', ''));
        $results = [];

        $curated = config('sapeur_pompiers_ci', []);
        if ($q === '') {
            $results = array_map(fn ($s) => array_merge($s, ['source' => 'curated']), $curated);
        } else {
            // Normalisation : insensible à la casse ET aux accents.
            $needle = $this->normalize($q);
            foreach ($curated as $s) {
                $haystack = $this->normalize(
                    ($s['name'] ?? '') . ' ' . ($s['commune'] ?? '') . ' ' . ($s['adresse'] ?? '')
                );
                if (str_contains($haystack, $needle)) {
                    $results[] = array_merge($s, ['source' => 'curated']);
                }
            }
        }

        if (mb_strlen($q) >= 3) {
            try {
                foreach ($this->searchOsmStations($q) as $entry) {
                    $key    = mb_strtolower($entry['name']) . '|' . round($entry['latitude'], 3);
                    $exists = false;
                    foreach ($results as $r) {
                        if (mb_strtolower($r['name']) . '|' . round($r['latitude'] ?? 0, 3) === $key) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $results[] = $entry;
                    }
                }
            } catch (\Throwable $e) {
                // Échec silencieux : on retourne au moins la liste curée.
            }
        }

        return response()->json([
            'success' => true,
            'count'   => count($results),
            'results' => array_slice($results, 0, 15),
        ]);
    }

    /**
     * Interroge l'API publique Nominatim (OpenStreetMap) pour trouver
     * des casernes correspondant au texte donné en Côte d'Ivoire.
     */
    protected function searchOsmStations(string $q): array
    {
        $query = $q . ' caserne OR sapeurs pompiers Côte d\'Ivoire';

        $response = Http::withHeaders([
            'User-Agent' => 'CAR225/1.0 (contact@maelysimo.com)',
            'Accept'     => 'application/json',
        ])
            ->timeout(6)
            ->get('https://nominatim.openstreetmap.org/search', [
                'q'              => $query,
                'format'         => 'json',
                'addressdetails' => 1,
                'countrycodes'   => 'ci',
                'limit'          => 8,
            ]);

        if (!$response->ok()) return [];

        $payload = $response->json() ?? [];
        $out     = [];
        foreach ($payload as $row) {
            $lat = isset($row['lat']) ? (float) $row['lat'] : null;
            $lon = isset($row['lon']) ? (float) $row['lon'] : null;
            if ($lat === null || $lon === null) continue;

            $address = $row['address'] ?? [];
            $commune = $address['city'] ?? $address['town'] ?? $address['village']
                ?? $address['suburb'] ?? $address['county'] ?? '';

            $displayName = $row['display_name'] ?? '';
            $shortAddr   = trim(implode(', ', array_slice(array_map('trim', explode(',', $displayName)), 0, 2)));
            $name        = $row['name'] ?? ($shortAddr ?: 'Caserne');

            $out[] = [
                'name'      => $name,
                'commune'   => $commune,
                'adresse'   => $shortAddr,
                'latitude'  => $lat,
                'longitude' => $lon,
                'source'    => 'osm',
            ];
        }
        return $out;
    }

    /**
     * Convertit en minuscules et retire les accents pour permettre une
     * recherche tolérante (ex: « bouake » trouve « Bouaké »).
     *
     * Utilise `Illuminate\Support\Str::ascii()` qui s'appuie sur une table
     * de translittération embarquée — fiable y compris sous Windows
     * (contrairement à `iconv` avec TRANSLIT).
     */
    protected function normalize(string $s): string
    {
        return Str::lower(Str::ascii($s));
    }

    /**
     * Cherche une caserne déjà enregistrée autour des coordonnées
     * fournies (~55m de tolérance ≈ 0.0005°). Permet d'empêcher la
     * création de plusieurs casernes au même endroit.
     *
     * Retourne `null` si :
     *  - les coordonnées ne sont pas fournies
     *  - aucune caserne ne correspond
     *
     * @param  float|null  $lat
     * @param  float|null  $lng
     * @param  int|null    $excludeId  ID à exclure (utile en update)
     */
    protected function findDuplicateByCoords(?float $lat, ?float $lng, ?int $excludeId = null): ?SapeurPompier
    {
        if ($lat === null || $lng === null) return null;

        $tolerance = 0.0005; // ~55 mètres

        $query = SapeurPompier::whereBetween('latitude',  [$lat - $tolerance, $lat + $tolerance])
                              ->whereBetween('longitude', [$lng - $tolerance, $lng + $tolerance]);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }
}
