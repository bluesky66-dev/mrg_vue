<?php
return [
    'import_path'                => env('IMPORT_SOURCE_PATH', storage_path('app' . DIRECTORY_SEPARATOR . 'imports')),
    'import_archive_path'        => env('IMPORT_ARCHIVE_PATH',
        storage_path('app' . DIRECTORY_SEPARATOR . 'imports' . DIRECTORY_SEPARATOR . 'archive')),
    'import_failure_path'        => env('IMPORT_FAILURE_PATH',
        storage_path('app' . DIRECTORY_SEPARATOR . 'imports' . DIRECTORY_SEPARATOR . 'failures')),
    'import_assets_reports_path' => env('IMPORT_ASSETS_REPORTS_PATH',
        storage_path('app' . DIRECTORY_SEPARATOR . 'imports' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'reports')),
    'import_assets_logos_path'   => env('IMPORT_ASSETS_LOGOS_PATH',
        storage_path('app' . DIRECTORY_SEPARATOR . 'imports' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'logos')),
    'participants_csv_prefix'    => 'Participants',
    'organizations_csv_prefix'   => 'Orgs',
    'report_scores_csv_prefix'   => 'Reporthistoryscores',
    'observers_csv_prefix'       => 'Observers',
];