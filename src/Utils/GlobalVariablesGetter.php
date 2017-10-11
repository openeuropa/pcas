<?php
namespace drupol\pcas\Utils;

/**
 * Class GlobalVariablesGetter.
 */
class GlobalVariablesGetter
{
    /**
     * Returns true if the $_REQUEST or $_GET variables has a key with $name.
     *
     * @param string $name
     *   The parameter name.
     *
     * @return bool
     *   True if it exists, false otherwise.
     */
    public static function has($name)
    {
        if (isset($_REQUEST[$name])) {
            return true;
        }

        return isset($_GET[$name]);
    }

    /**
     * Returns the value in $_REQUEST[$name] or $_GET[$name] if the former was empty. If no value found, return null.
     *
     * @param string $name
     *   The parameter name.
     *
     * @return string|null
     *   The value of the parameter.
     */
    public static function get($name)
    {
        return filter_input(INPUT_GET, $name, FILTER_SANITIZE_STRING);
    }
}
