<?php

namespace App\Traits;

trait HasCodeId
{
    /**
     * Boot the trait - auto-generate code_id on creation.
     */
    public static function bootHasCodeId()
    {
        static::creating(function ($model) {
            // Ne pas générer de code_id pour les utilisateurs Google
            if ($model instanceof \App\Models\User && !empty($model->google_id)) {
                return;
            }

            if (empty($model->code_id)) {
                $model->code_id = static::generateUniqueCodeId();
            }
        });
    }

    /**
     * Generate a unique code_id with a prefix based on the model type.
     * Format: PREFIX-XXXXXX (ex: USR-482913, AGT-193847)
     */
    public static function generateUniqueCodeId(): string
    {
        $prefixes = [
            'User'      => 'USR',
            'Agent'     => 'AGT',
            'Hotesse'   => 'HTS',
            'Caisse'    => 'CSS',
            'Personnel' => 'CHF',
            'Gare'      => 'GAR',
        ];

        $className = class_basename(static::class);
        $prefix = $prefixes[$className] ?? 'COD';

        do {
            $code = $prefix . '-' . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('code_id', $code)->exists());

        return $code;
    }
}
