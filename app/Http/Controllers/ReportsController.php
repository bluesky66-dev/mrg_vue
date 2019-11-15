<?php

namespace Momentum\Http\Controllers;

use Momentum\Report;

/**
 * Handles requests related with the reports.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class ReportsController extends Controller
{
    /**
     * Returns a download response to a specified report.
     * @since 0.2.4
     * 
     * @param int $id Report ID to download.
     *
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $report = Report::currentUser()->active()->findOrFail($id);

        $file = config('momentum.reports_path') . DIRECTORY_SEPARATOR . $report->file;

        if (!file_exists($file)) {
            abort(404);
        }

        return response()->download($file);
    }
}
