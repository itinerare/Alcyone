<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | This is a list of general site settings that should not be changed
    | frequently.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Image Settings
    |--------------------------------------------------------------------------
    |
    | These are settings related to the site's images.
    |
    */

    // Image dimensions, in px. Note that these settings are not retroactive!
    'thumbnail_height'   => 200,
    'display_image_size' => 2000,

    /*
    |--------------------------------------------------------------------------
    | Enable Backups
    |--------------------------------------------------------------------------
    |
    | This feature will create a daily backup automatically. Requires a cron job
    | to be set up as well!
    | Note that it's recommended to configure config/backup.php as desired as well,
    | especially to adjust the location backups are saved to (by default, they are
    | saved locally). Note that even if this is disabled, backups can still be ran
    | manually using the backup:run command.
    |
    | Simply change to "1" to enable, or keep at "0" to disable.
    |
    */

    'enable_backups' => 0,

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    |
    | This is the current version of Alcyone that your site is using.
    | Do not change this value!
    |
    */

    'version' => '1.0.0',
];
