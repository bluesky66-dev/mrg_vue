<?php

namespace Momentum\Enums;

use Momentum\Utilities\Enum;

/**
 * Constants/enums for observer types.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class ObserverTypes extends Enum
{
    const BOSS = 'boss';
    const PEER = 'peer';
    const DIRECT_REPORT = 'direct_report';
    const OTHER = 'other';

    /**
     * Mapping definitions between quest variables and this constants.
     * @since 0.2.4
     * 
     * @var array
     */
    protected static $quest_map = [
        'Boss'          => self::BOSS,
        'Peer'          => self::PEER,
        'Direct Report' => self::DIRECT_REPORT,
        'Other'         => self::OTHER,
    ];

    /**
     * Returns the app's const/enum mapped to a quest type.
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
     * @see {resources}/lang/global.php['enum']['observer_types']
     * 
     * @return array
     */
    public static function optionsWithLabels()
    {
        $options = [];
        $values = self::options();
        foreach ($values as $value) {
            $options[$value] = trans('global.enum.observer_types.' . $value . '.label');
        }
        
        return $options;
    }
}