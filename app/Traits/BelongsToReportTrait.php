<?php

namespace Momentum\Traits;

use App;
use Exception;
use Momentum\Report;

/**
 * Trait that adds basic relationship functionality between 
 * a model and a report.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
trait BelongsToReportTrait
{
    /**
     * Returns scoped query, filtered it by report.
     * @since 0.2.4
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query  Query builder.
     * @param Report                                $report Report to filter.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereReport($query, Report $report)
    {
        return $query->where('report_id', $report->id);
    }
    /**
     * Returns scoped query, filtered by current/active report.
     * @since 0.2.4
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentReport($query)
    {
        try {
            $report = App::make('ActiveReport');
        } catch (Exception $e) {
            // return a query that will have no results
            return $query->where('id', null);
        }
        if (!$report) {
            // return a query that will have no results
            return $query->where('id', null);
        }
        // query this item for the report id
        return $query->where('report_id', $report->id);
    }
}