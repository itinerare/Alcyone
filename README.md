# Alcyone

Alcyone is a lightweight small-scale/private image hosting application. It supports multiple users, with registration restricted via invitation code. Image uploads are private per user, with a content report flow if necessary, accessible without registration. Images are stored in webP for storage and performance savings, with the ability to generate and cache a png version for convenience.

- Support Discord: https://discord.gg/mVqUzgQXMd

### Features
- Support for multiple users, with permissions handled via a lightweight rank system (user/moderator/admin)
- Users can upload (by default) images of up to 17MB, only visible personally on the site
    - Images are converted (if necessary) to webP for storage and performance savings
    - Images may be accessed via either a "web URL" (webP) or "share URL" (png); png images are cached for 24 hours and then removed to save on storage space
    - Users may delete their own images at will
- A content report flow; content reports are created semi-anonymously (IP and optionally email address are recorded, the latter for notifications re report(s) only) and do not require registration
    - A report page accessible via key for access by the reporter
    - Reports are added to a queue accessible by moderators and admins, where they may be accepted (deleting the reported image), cancelled (preserving it), or the reporter banned (in the event of abuse of the report system)
    - Optional email notifications for the reporter on processing a report
- Notifications (presently only used to notify an image's uploader that an image was removed as a result of a content report)
- Light and dark themes (defaults to dark theme, adjustable in user settings for registered users)
- 2FA for registered users
- An invitation key system; invitation keys are necessary to register and may only be generated by site admin(s)
- Minor rank editing (name and description), accessible only to admins
- User index and editing, accessible only to admins
- On-site editing for the terms of service and privacy policy pages
- A lightweight admin/moderator panel
    - Moderators may access the index and reports queue
    - Admins have access to all functions
- Backup handling including optional Dropbox support

## Setup

### Obtain a copy of the code

```
$ git clone https://code.itinerare.net/itinerare/alcyone.git
```

### Configure .env in the directory

```
$ cp .env.example .env
```

You will need to provide email information; in a pinch services that allow sending email via SMTP will do.

### Setting up

Install packages with composer:
```
$ composer install
```

Generate app key, create the database, and run migrations:
```
$ php artisan key:generate
$ php artisan migrate
```

Perform general site setup:
```
$ php artisan setup-alcyone
```

Optionally, configure your time zone in `config/app.php` (see [here](https://www.php.net/manual/en/timezones.php) for a list of PHP-supported time zones).

Ensure that the scheduler is added to cron, like so:
```
* * * * * cd ~/site-name.com/www && php artisan schedule:run >> /dev/null 2>&1
```

## Contact
If you have any questions, please contact me via email at [queries@itinerare.net](emailto:queries@itinerare.net).
