<?php

namespace Momentum\Enums;

use Momentum\Utilities\Enum;

/**
 * Constants/enums for action plan reminder types.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class ActionPlanReminderTypes extends Enum
{
    const REVIEW = 'review';
    const PULSE_SURVEYS = 'pulse_surveys';
    const ACTION_STEP = 'action_step';
}