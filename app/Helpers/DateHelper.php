<?php

namespace App\Helpers;

class DateHelper
{
    public static function getFrenchDayFromEnglish($englishDay)
    {
        $days = [
            'monday' => 'lundi',
            'tuesday' => 'mardi',
            'wednesday' => 'mercredi',
            'thursday' => 'jeudi',
            'friday' => 'vendredi',
            'saturday' => 'samedi',
            'sunday' => 'dimanche'
        ];
        
        return $days[strtolower($englishDay)] ?? $englishDay;
    }
    
    public static function getEnglishDayFromFrench($frenchDay)
    {
        $days = [
            'lundi' => 'monday',
            'mardi' => 'tuesday',
            'mercredi' => 'wednesday',
            'jeudi' => 'thursday',
            'vendredi' => 'friday',
            'samedi' => 'saturday',
            'dimanche' => 'sunday'
        ];
        
        return $days[strtolower($frenchDay)] ?? $frenchDay;
    }
}