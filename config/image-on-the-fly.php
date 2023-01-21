<?php

return [

    /*
    |--------------------------------------------------------------------------
    | image on-the-fly 即時產圖
    |--------------------------------------------------------------------------
    */

    // 圖檔來源
    'source_disk' => env('IMAGE_ON_THE_FLY_SOURCE_DISK', 'on-the-fly-images'),
    'source_folder' => env('IMAGE_ON_THE_FLY_SOURCE_FOLDER', ''),

    // 預設寬高
    'default_width' => env('IMAGE_ON_THE_FLY_DEFAULT_WIDTH', 300),
    'default_height' => env('IMAGE_ON_THE_FLY_DEFAULT_HEIGHT', 300),

    // 快取設定
    'no_cache' => env('IMAGE_ON_THE_FLY_NO_CACHE', false),
    'cache_disk' => env('IMAGE_ON_THE_FLY_CACHE_DISK', 'public'),
    'cache_folder' => env('IMAGE_ON_THE_FLY_CACHE_FOLDER', 'images'),
    'allow_renew' => env('IMAGE_ON_THE_FLY_ALLOW_RENEW', false),
];
