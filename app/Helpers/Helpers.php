<?php

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
|
| Miscellaneous helper functions, primarily used for formatting.
|
*/

/**
 * Returns class name if the current URL corresponds to the given path.
 *
 * @param string $path
 * @param string $class
 *
 * @return string
 */
function set_active($path, $class = 'active') {
    return call_user_func_array('Request::is', (array) $path) ? $class : '';
}

/**
 * Adds a help icon with a tooltip.
 *
 * @param string $text
 *
 * @return string
 */
function add_help($text) {
    return '<i class="fas fa-question-circle help-icon text-info" data-bs-toggle="tooltip" title="'.$text.'"></i>';
}

/**
 * Uses the given array to generate breadcrumb links.
 *
 * @param array $links
 *
 * @return string
 */
function breadcrumbs($links) {
    $ret = '<nav><ol class="breadcrumb">';
    $count = 0;
    $ret .= '<li class="breadcrumb-item"><a href="'.url('/').'">Home</a></li>';
    foreach ($links as $key => $link) {
        $isLast = ($count == count($links) - 1);

        $ret .= '<li class="breadcrumb-item ';
        if ($isLast) {
            $ret .= 'active';
        }
        $ret .= '">';

        if (!$isLast) {
            $ret .= '<a href="'.url($link).'">';
        }
        $ret .= $key;
        if (!$isLast) {
            $ret .= '</a>';
        }

        $ret .= '</li>';

        $count++;
    }
    $ret .= '</ol></nav>';

    return $ret;
}

/**
 * Formats the timestamp to a standard format.
 *
 * @param Illuminate\Support\Carbon\Carbon $timestamp
 * @param bool                             $showTime
 *
 * @return string
 */
function format_date($timestamp, $showTime = true) {
    return $timestamp->format('j F Y'.($showTime ? ', H:i:s' : '')).($showTime ? ' <abbr data-bs-toggle="tooltip" title="UTC'.$timestamp->timezone->toOffsetName().'">'.strtoupper($timestamp->timezone->getAbbreviatedName($timestamp->isDST())).'</abbr>' : '');
}

/**
 * Formats the timestamp to a standard format.
 *
 * @param Illuminate\Support\Carbon\Carbon $timestamp
 * @param bool                             $showTime
 *
 * @return string
 */
function pretty_date($timestamp, $showTime = true) {
    return '<abbr data-bs-toggle="tooltip" title="'.$timestamp->format('F j Y'.($showTime ? ', H:i:s' : '')).' '.strtoupper($timestamp->timezone->getAbbreviatedName($timestamp->isDST())).'">'.$timestamp->diffForHumans().'</abbr>';
}

/**
 * Generates a string of random characters of the specified length.
 *
 * @param int $characters
 *
 * @return string
 */
function randomString($characters) {
    $src = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $code = '';
    for ($i = 0; $i < $characters; $i++) {
        $code .= $src[mt_rand(0, strlen($src) - 1)];
    }

    return $code;
}
