<?php
return [
    'max_char_length'       => 520,
    'pdf_path'              => 'pdfs',
    'reports_path'          => storage_path('app' . DIRECTORY_SEPARATOR . 'reports'),
    'logos_path'            => public_path('images' . DIRECTORY_SEPARATOR . 'organization-logos'),
    'logos_asset_path'      => 'images' . DIRECTORY_SEPARATOR . 'organization-logos',
    'pdf_generator_command' => env('PDF_GENERATOR_COMMAND', 'node /home/vagrant/url-2-pdf/url-2-pdf.js'),
    'long_date_format'      => '%b %d, %Y',
    'token_expiration'      => 432000, // seconds in hour * hours * days: 3600 * 24 * 5 = 432000
    'ga_tracking_id'        => env('GA_TRACKING_ID', 'UA-XXXXX-Y'),

    /*
    |--------------------------------------------------------------------------
    | Labels separator
    |--------------------------------------------------------------------------
    |
    | This is used to separate multiple labels in a string.
    */
    'labels_separator' => ', ',

    /*
    |--------------------------------------------------------------------------
    | Organization configurations
    |--------------------------------------------------------------------------
    */
    'organization' => [

        // Default goal language keys
        'goals' => [
            [
                'key' => 'action_plan.goals.benefits.help_text',
                'type' => 'benefits',
                'sort' => 0,
            ],
            [
                'key' => 'action_plan.goals.constituents.help_text',
                'type' => 'constituents',
                'sort' => 1,
            ],
            [
                'key' => 'action_plan.goals.goal.help_text',
                'type' => 'goal',
                'sort' => 2,
            ],
            [
                'key' => 'action_plan.goals.obstacles.help_text',
                'type' => 'obstacles',
                'sort' => 3,
            ],
            [
                'key' => 'action_plan.goals.resources.help_text',
                'type' => 'resources',
                'sort' => 4,
            ],
            [
                'key' => 'action_plan.goals.risks.help_text',
                'type' => 'risks',
                'sort' => 5,
            ],
        ],
    ],
];