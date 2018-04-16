<?php

namespace Modules\Product\Helpers;

class GlobalHelpers
{
    /**
     * Get the human readable file size.
     *
     * @param int $size
     * @param int $decimals
     * @return string
     */
    public static function convertToMb(int $size, int $decimals = 2): string
    {
        $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($size) - 1) / 3);

        return sprintf("%.{$decimals}f", $size / pow(1024, $factor)) . ' ' . @$sizes[$factor];
    }
}