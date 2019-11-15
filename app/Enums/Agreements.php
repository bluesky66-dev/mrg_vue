<?php

namespace Momentum\Enums;

use Momentum\Utilities\Enum;

/**
 * Constants/enums for agreements.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class Agreements extends Enum
{
    const LOW = 'low';
    const MEDIUM = 'medium';
    const HIGH = 'high';

    /**
     * Mapping definitions between quest variables and this constants.
     * @since 0.2.4
     * 
     * @var array
     */
    protected static $quest_map = [
        'Low' => self::LOW,
        'Medium' => self::MEDIUM,
        'High' => self::HIGH,
    ];

    /**
     * Returns the app's const/enum mapped to a quest agreement.
     * @since 0.2.4
     * 
     * @param string $key Quest key.
     * 
     * @return string
     */
    public static function mapFromQuest($key)
    {
        if (!isset(self::$quest_map[$key])) {
            return null;
        }
        return self::$quest_map[$key];
    }

    /**
     * Returns the translations for the const/enums.
     * @since 0.2.4
     * 
     * @see {resources}/lang/global.php['enum']['agreements']
     * 
     * @return array
     */
    public static function optionsWithLabels()
    {
        $options = [];
        $values = self::options();
        foreach ($values as $value) {
            $options[$value] = trans('global.enum.agreements.' . $value . '.label');
        }

        return $options;
    }
}