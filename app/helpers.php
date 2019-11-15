<?php

use Momentum\User;
use Momentum\AnalyticsEvent;

/**
 * Global function helpers.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */

if (!function_exists('track_event')) {
    /**
     * Tracks an event for analytics purposes.
     * @since 0.2.4
     *
     * @param \Momentum\User $user     User related to event.
     * @param string         $category Event category.
     * @param string         $action   Event action.
     * @param string         $label    Event label.
     * @param mixed          $data     Data.
     * 
     * @return \Momentum\AnalyticsEvent
     */
    function track_event(User $user = null, $category, $action = null, $label = null, $data = null)
    {
        return AnalyticsEvent::create([
            'user_id'  => $user->id,
            'category' => $category,
            'action'   => $action,
            'label'    => $label,
            'data'     => $data,
        ]);

    }
}
