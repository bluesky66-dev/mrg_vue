<?php

namespace Momentum\Utilities;

use App;
use Momentum\Culture;

/**
 * Localization utility class the functionality to set the application locale
 * based on a Culture model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class Localization
{
    /**
     * Sets application locate based on culture.
     * @since 0.2.4
     * 
     * @param \Momentum\Culture $culture Culture model.
     */
    public static function setApplicationLocale(Culture $culture)
    {
        App::setLocale($culture->code);
        setlocale(LC_ALL, $culture->code_unix);
        App::instance('CurrentCulture', $culture);
    }
}