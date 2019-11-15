<?php


namespace Momentum\Utilities;

use Illuminate\Support\Collection;

/**
 * Statistics utility class provides calculation methods needed to
 * generate reports and various statistical values.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class Statistics
{
    /**
     * Calculates the mean value of an array.
     * @since 0.2.4
     * 
     * @param array $values
     * 
     * @return float
     */
    public static function calculateMean($values)
    {
        if (empty($values)) {
            return 0;
        }
        return array_sum($values) / count($values);
    }

    /**
     * Calculates the standard deviation value of an array.
     * @since 0.2.4
     * 
     * @param array $values
     * 
     * @return float
     */
    public static function calculateStandardDeviation($values)
    {
        if (count($values) < 2) {
            return null;
        }

        return sqrt(array_sum(array_map("self::calculateStandardDeviationSquare", $values,
                array_fill(0, count($values), (array_sum($values) / count($values))))) / (count($values) - 1));
    }

    /**
     * Calculates the standard deviation quare value of an array.
     * @since 0.2.4
     * 
     * @param array $values Values array
     * @param float $mean   Mean value.
     * 
     * @return float
     */
    public static function calculateStandardDeviationSquare($value, $mean)
    {
        return pow($value - $mean, 2);
    }

    /**
     * Calculates cycle value of an array.
     * @since 0.2.4
     * 
     * @param array $values
     * 
     * @return array
     */
    public static function calculateCycle($values)
    {
        $mean = self::calculateMean($values);
        $sd = self::calculateStandardDeviation($values, $mean);

        $sd_rounded = round($sd, 1);
        $mean_rounded = round($mean, 1);

        $min = $mean - $sd;
        $max = $mean + $sd;

        $min_rounded = $mean_rounded - $sd_rounded;
        $max_rounded = $mean_rounded + $sd_rounded;
        
        // This is to make the chart not go off screen.
        if($max_rounded > 7){
            $max_rounded = 7.75;
        }
        if($min_rounded < 1){
            $min_rounded = 0.25;
        }

        return [
            'min'                    => $min_rounded,
            'mean'                   => $mean_rounded,
            'standard_deviation'     => $sd_rounded,
            'max'                    => $max_rounded,
            'min_raw'                => $min,
            'mean_raw'               => $mean,
            'standard_deviation_raw' => $sd,
            'max_raw'                => $max,
        ];
    }

    /**
     * Calculates the cycles of a collection of arrays.
     * @since 0.2.4
     * 
     * @param Illuminate\Support\Collection $collection
     * 
     * @return array
     */
    public static function calculateCycleFromCollection(Collection $collection)
    {
        // get all the scores from the collection
        $values = $collection->pluck('score');

        // filter out all the null values
        $values = $values->filter(function ($value) {
            // filter out people who didn't answer, and people who answered "N/A"
            if ($value == null || $value == -1) {
                return false;
            }

            return true;
        });

        $values = $values->toArray();

        return self::calculateCycle($values);
    }
}