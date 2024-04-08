<?php

namespace App\Services;

interface RosterParsingServiceInterface
{
    public const ALLOWED_ACTIVITY_TYPES = [
        'OFF',
        'SBY',
        'DO',
        'FLT',
        'CI',
        'CO'
    ];

    public const FLIGHT_PATTERN = '/^[A-Za-z]{2}\d*$/';

    public function parseRoster($file): array;
}