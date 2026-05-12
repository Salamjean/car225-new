<?php

namespace App\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Vérifie un identityToken (JWT) émis par Apple lors d'un Sign in with Apple.
 *
 * Procédure :
 *  1. Récupère les clés publiques d'Apple (JWKS) depuis
 *     https://appleid.apple.com/auth/keys (mises en cache 24h).
 *  2. Vérifie la signature du JWT, son expiration, l'émetteur et l'audience.
 *  3. Retourne les claims décodés : `sub` (identifiant Apple unique),
 *     `email`, `email_verified`, etc.
 *
 * Configuration : voir `config/services.php` clé `apple` (audience(s) acceptée(s)).
 */
class AppleAuthService
{
    private const APPLE_KEYS_URL  = 'https://appleid.apple.com/auth/keys';
    private const APPLE_ISSUER    = 'https://appleid.apple.com';
    private const CACHE_KEY       = 'apple_jwks_keys';
    private const CACHE_TTL_HOURS = 24;

    /**
     * Vérifie le JWT et retourne ses claims.
     *
     * @throws RuntimeException si le token est invalide.
     * @return array{sub:string, email?:string, email_verified?:bool, aud:string}
     */
    public function verifyIdentityToken(string $idToken): array
    {
        // 1. Décodage avec vérification de la signature via JWKS Apple
        try {
            $keys    = $this->getApplePublicKeys();
            $decoded = JWT::decode($idToken, $keys);
            $payload = (array) $decoded;
        } catch (\Throwable $e) {
            Log::warning('Apple JWT verification failed: ' . $e->getMessage());
            throw new RuntimeException('Token Apple invalide : ' . $e->getMessage(), 0, $e);
        }

        // 2. Vérif émetteur
        if (($payload['iss'] ?? null) !== self::APPLE_ISSUER) {
            throw new RuntimeException('Émetteur Apple incorrect.');
        }

        // 3. Vérif audience (Bundle ID iOS et/ou Service ID Web — configurable)
        $allowedAudiences = $this->getAllowedAudiences();
        $tokenAud         = $payload['aud'] ?? null;

        if (!$allowedAudiences) {
            // Avertissement : aucune audience configurée → on log mais on
            // accepte (tolérance dev — ne PAS faire en production).
            Log::warning('AppleAuthService : aucune audience configurée dans services.apple.client_ids');
        } elseif (!in_array($tokenAud, $allowedAudiences, true)) {
            throw new RuntimeException('Audience Apple non autorisée : ' . $tokenAud);
        }

        // 4. Vérif sub
        if (empty($payload['sub'])) {
            throw new RuntimeException('Identifiant Apple (sub) manquant.');
        }

        return $payload;
    }

    /**
     * Récupère et met en cache les clés publiques Apple (JWKS).
     *
     * ⚠️ On cache uniquement le JSON brut (tableau PHP simple, sérialisable).
     * JWK::parseKeySet() crée des OpenSSLAsymmetricKey non sérialisables —
     * on l'appelle APRÈS la lecture du cache pour éviter l'erreur de sérialisation.
     */
    private function getApplePublicKeys(): array
    {
        // 1. Cache du JSON brut uniquement (sérialisable)
        $jwks = Cache::remember(self::CACHE_KEY, now()->addHours(self::CACHE_TTL_HOURS), function () {
            $response = Http::timeout(8)->get(self::APPLE_KEYS_URL);
            if (!$response->ok()) {
                throw new RuntimeException('Impossible de récupérer les clés publiques d\'Apple.');
            }
            return $response->json(); // ← tableau PHP brut, pas d'OpenSSL
        });

        // 2. Parse les clés en mémoire (jamais mis en cache)
        return JWK::parseKeySet($jwks);
    }

    /**
     * Liste des audiences autorisées (bundle id iOS / service id web).
     * Configurable via `config/services.php` clé `apple.client_ids`.
     */
    private function getAllowedAudiences(): array
    {
        $raw = config('services.apple.client_ids', []);
        if (is_string($raw)) {
            $raw = array_map('trim', explode(',', $raw));
        }
        return array_values(array_filter($raw));
    }
}
