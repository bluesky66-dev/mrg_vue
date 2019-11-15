<?php


namespace Momentum\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App;
use Exception;

/**
 * Global scope set for when an active report is present in the application.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class ActiveReportScope implements Scope
{
    /**
     * Apply scope.
     * @since 0.2.4
     *
     * @param Illuminate\Database\Eloquent\Builder $builder
     * @param Illuminate\Database\Eloquent\Model   $model
     * 
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $builder, Model $model)
    {
        try {
            $report = App::make('ActiveReport');
        } catch (Exception $e) {
            // return a query that will have no results
            return $builder->where('id', null);
        }

        if (!$report) {
            // return a query that will have no results
            return $builder->where('id', null);
        }

        // query this item for the report id
        return $builder->where('report_id', $report->id);
    }

}