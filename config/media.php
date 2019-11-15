<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media available drivers
    |--------------------------------------------------------------------------
    |
    | Indicates the driver to user. Since this is custom, there is no much to select.
    | @link https://laravel.com/docs/5.6/filesystem#the-public-disk
    |
    */
    'driver' => 'laravel',

    'drivers' => [
        'laravel' => [
            'storage' => 'disk:local',
            'unique_name' => true, // Indicates if filename should be replaced and make it unique
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Relative upload path
    |--------------------------------------------------------------------------
    |
    | Indicates the relative path in which new uploads should be stored.
    | For example, if file name is "image.png" and today's date is "2018-07-04",
    | then image will be stored as "uploads/2018/07/image.png"
    |
    */
    'relative_path' => 'uploads/'.date('Y').'/'.date('m').'/',

    /*
    |--------------------------------------------------------------------------
    | Supported mime types
    |--------------------------------------------------------------------------
    |
    | List of mimes supported.
    |
    */
    'mimes' => [
        'image/jpeg',
        'image/png',
        'image/svg+xml',
        'image/gif',
        'video/mp4',
        'application/pdf',
        'text/plain',
        'text/rtf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'application/rtf',
        'application/vnd.ms-excel',
        'application/vnd.ms-powerpoint',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload sizes (bytes)
    |--------------------------------------------------------------------------
    */
    'min_size' => 100, // 100 bytes
    'max_size' => 5 * 1000000, // 5 megabytes

];
