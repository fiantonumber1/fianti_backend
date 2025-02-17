<?php

use Laravel\Telescope\Http\Middleware\Authorize;
use Laravel\Telescope\Watchers;

return [

    /*
    |--------------------------------------------------------------------------
    | Telescope Default Watchers
    |--------------------------------------------------------------------------
    |
    | The following array lists the "watchers" that are enabled by default
    | for your application. Feel free to enable or disable these watchers
    | as needed.
    |
    */

    'watchers' => [
        \Laravel\Telescope\Watchers\RequestWatcher::class => env('TELESCOPE_WATCH_REQUESTS', true),
        \Laravel\Telescope\Watchers\CommandWatcher::class => env('TELESCOPE_WATCH_COMMANDS', true),
        \Laravel\Telescope\Watchers\JobWatcher::class => env('TELESCOPE_WATCH_JOBS', true),
        \Laravel\Telescope\Watchers\LogWatcher::class => env('TELESCOPE_WATCH_LOGS', true),
        \Laravel\Telescope\Watchers\QueryWatcher::class => env('TELESCOPE_WATCH_QUERIES', true),
        \Laravel\Telescope\Watchers\ViewWatcher::class => env('TELESCOPE_WATCH_VIEWS', true),
        \Laravel\Telescope\Watchers\CacheWatcher::class => env('TELESCOPE_WATCH_CACHE', true),
        \Laravel\Telescope\Watchers\RedisWatcher::class => env('TELESCOPE_WATCH_REDIS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Telescope Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether Telescope is enabled for your application.
    | When this option is disabled, Telescope's features will not be available.
    | You can still see the Telescope UI if it is enabled in your environment.
    |
    */

    'enabled' => env('TELESCOPE_ENABLED', true),

    // Other configurations...
];

