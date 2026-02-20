<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'label'
    ];

    public static function isTicketSystemEnabled()
    {
        $setting = self::where('key', 'is_ticket_system_enabled')->first();
        return $setting ? (bool)$setting->value : true; // Default to true if not found
    }

    public static function isMaintenanceMode()
    {
        $setting = self::where('key', 'maintenance_mode')->first();
        return $setting ? (bool)$setting->value : false;
    }

    public static function getMaintenanceMessage()
    {
        $setting = self::where('key', 'maintenance_message')->first();
        return $setting ? $setting->value : 'Notre plateforme est actuellement en maintenance.';
    }
}
