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
    | Site Name
    |--------------------------------------------------------------------------
    |
    | This differs from the app name in that it is allowed to contain spaces
    | (APP_NAME in .env cannot take spaces). This will be displayed on the
    | site wherever the name needs to be displayed.
    |
    */
    'site_name' => 'Alcyone',

    /*
    |--------------------------------------------------------------------------
    | Site Description
    |--------------------------------------------------------------------------
    |
    | This is the description used for the site in meta tags-- previews
    | displayed on various social media sites, discord, and the like.
    | It is not, however, displayed on the site itself. This should be kept short and snappy!
    |
    */
    'site_desc' => 'An Alcyone site',

    /*
    |--------------------------------------------------------------------------
    | Image Settings
    |--------------------------------------------------------------------------
    |
    | These are settings related to the site's images.
    |
    */

    // Image dimensions, in px. Note that these settings are not retroactive!
    'thumbnail_height' => 200,

    // Hours that converted images are held for before being deleted.
    'cache_lifetime' => 24,

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

    'version' => '1.1.0',
];
