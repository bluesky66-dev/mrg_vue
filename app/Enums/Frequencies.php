<?php

namespace Momentum\Enums;

use Momentum\Utilities\Enum;

/**
 * Constants/enums for frequencies.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class Frequencies extends Enum
{
    const ONCE = 'once';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';

    /**
     * Returns the translations for the const/enums.
     * @since 0.2.4
     * 
     * @see {resources}/lang/global.php['enum']['frequencies']
     * 
     * @return array
     */
    public static function optionsWithLabels()
    {
        $options = [];
        $values = self::options();
        foreach ($values as $value) {
            $options[$value] = trans('global.enum.frequencies.' . $value . '.label');
        }

        return $options;
    }
}
