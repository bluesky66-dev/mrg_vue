<?php

namespace Momentum\Services;

use GuzzleHttp\Client;
use Momentum\AnalyticsEvent;

/**
 * Analytics service.
 * Provides functionality surrounding Google and other type of analytics.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class AnalyticsService
{
    /**
     * Tracks an event for analytics purposes.
     * Returns flag indicating if tracking has been succesfull.
     * @since 0.2.4
     *
     * @param string         $category Event category.
     * @param string         $action   Event action.
     * @param string         $label    Event label.
     * @param mixed          $value    Value.
     * @param mixed          $data     Data.
     * 
     * @return bool
     */
    public static function trackEvent($category, $action, $label = null, $value = null, $data = null)
    {
        $event = new AnalyticsEvent([
            'category' => $category,
            'action'   => $action,
            'label'    => $label,
            'value'    => $value,
            'data'     => $data,
        ]);

        if (\Auth::check()) {
            $event->user_id = \Auth::user()->id;
        }

        $event->save();

        $client = new Client();

        $url = self::buildAnalyticsArray($category, $action, $label, $value);

        try {
            $response = $client->post($url);
        } Catch (\Exception $e) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        return true;
    }

    /**
     * Builds google analytics modules array for tracking events.
     * Returns analytics url.
     * @since 0.2.4
     *
     * @param string         $category Event category.
     * @param string         $action   Event action.
     * @param string         $label    Event label.
     * @param mixed          $value    Value.
     * 
     * @return string
     */
    private static function buildAnalyticsArray($category, $action, $label = null, $value = null)
    {
        $url = 'https://www.google-analytics.com/collect?';
        $data = [
            'v'   => 1,
            'tid' => config('momentum.ga_tracking_id'),
            'cid' => uniqid(),
            't'   => 'event',
            'ec'  => $category,
            'ea'  => $action,
        ];

        if ($label) {
            $data['el'] = $label;
        }

        if ($value) {
            $data['ev'] = $value;
        }

        return $url . http_build_query($data);
    }
}