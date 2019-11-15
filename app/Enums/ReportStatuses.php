<?php

namespace Momentum\Enums;

use Momentum\Utilities\Enum;

/**
 * Constants/enums for report statuses.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class ReportStatuses extends Enum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const DELETED = 'deleted';
}