<?php


namespace Momentum\Utilities;

use Carbon\Carbon;

/**
 * Dates utility class provides functionality to normalizes, 
 * standarizes and formats date values.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class Dates
{
    /**
     * Returns the closest available date from NOW, based on a collection of dates.
     * @since 0.2.4
     * 
     * @param array|Collection $dates Dates.
     * 
     * @return string
     */
    public static function getNextDate($dates)
    {
        // get all the dates that are in the future
        $future_dates = $dates->filter(function($date){
            return $date >= Carbon::now()->startOfDay();
        });

        // sort all the dates in order (they already should be, but just in case)
        $sorted = $future_dates->sortByDesc('date');

        // grab the first one
        return $sorted->first();
    }

    /**
     * Returns the localization, timestamp and iso8601 value of a date.
     * @since 0.2.4
     * 
     * @param Carbon\Carbon $date Carbon date.
     * 
     * @return array
     */
    public static function returnFormattedDates($date = null)
    {
        if ($date == null || !$date instanceof Carbon) {
            return [
                'localized' => null,
                'iso8601'   => null,
                'timestamp' => null,
            ];
        }
        return [
            'localized' => $date->formatLocalized(config('momentum.long_date_format')),
            'iso8601'   => $date->toIso8601String(),
            'timestamp' => (int)$date->format('U'),
        ];
    }
}