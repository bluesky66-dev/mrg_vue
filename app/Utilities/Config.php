<?php

namespace Momentum\Utilities;

/**
 * Configuration utility class.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class Config
{
    /**
     * Returns configuration values for front-end purposes.
     * Configuration for HTML, JavaScript and CSS processing.
     * @since 0.2.4
     * 
     * @see {config}/momentum.php
     *
     * @return string JSON encoded.
     */
    public static function getConfigForFrontend()
    {
        return json_encode([
            'max_char_length' => config('momentum.max_char_length'),
        ]);
    }
}