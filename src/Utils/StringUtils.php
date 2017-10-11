<?php
namespace drupol\pcas\Utils;

/**
 * Class StringUtils.
 */
class StringUtils
{
    /**
     * Check if a string is not empty.
     *
     * @param string $val
     *   The string.
     *
     * @return bool
     *   True if not empty, false otherwise.
     */
    public static function isNotEmpty($val)
    {
        return $val
        && !is_null($val)
        && sizeof($val) > 0
        && '' != trim($val);
    }

    /**
     * Check if a string is empty.
     *
     * @param string $val
     *   The string.
     *
     * @return bool
     *   True if empty, false otherwise.
     */
    public static function isEmpty($val)
    {
        return !static::isNotEmpty($val);
    }
}
