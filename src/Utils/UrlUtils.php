<?php
namespace drupol\pcas\Utils;

/**
 * Class UrlUtils.
 */
class UrlUtils
{
    /**
     * Check if an URL is valid.
     *
     * @param string $url
     *   The url to check.
     *
     * @return bool
     *   True if valid, false otherwise.
     */
    public static function isValidUrl($url)
    {
        return StringUtils::isNotEmpty($url)
            && false !== filter_var($url, FILTER_VALIDATE_URL);
    }
}
