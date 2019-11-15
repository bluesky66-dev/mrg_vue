<?php

namespace Momentum\Events;

use Momentum\ApplicationState;
use Illuminate\Queue\SerializesModels;

/**
 * Event called whenever an application state is updated or created.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ApplicationStateUpdated
{
    use SerializesModels;
    /**
     * Application state.
     * @since 0.2.5
     *
     * @var object 
     */
    public $state;
    /**
     * Constructor.
     * @since 0.2.5
     *
     * @param ApplicationState $state
     */
    public function __construct(ApplicationState $state)
    {
        $this->state = $state;
    }
}