<?php

namespace Momentum\Enums;

use Momentum\Utilities\Enum;

/**
 * Constants/enums for emphasis.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class Emphasis extends Enum
{
    const MORE = 'more';
    const LESS = 'less';

    /**
     * Returns the translations for the const/enums.
     * @since 0.2.4
     * 
     * @see {resources}/lang/global.php['enum']['emphasis']
     * 
     * @return array
     */
    public static function optionsWithLabels()
    {
        $options = [];
        $values = self::options();
        foreach ($values as $value) {
            $options[$value] = trans('global.enum.emphasis.' . $value . '.label');
        }

        return $options;
    }
}