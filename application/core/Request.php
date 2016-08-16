<?php

/**
 * This is under development. Expect changes!
 * Class Request
 * Abstracts the access to $_GET, $_POST and $_COOKIE, preventing direct access to these super-globals.
 * This makes PHP code quality analyzer tools very happy.
 * @see http://php.net/manual/en/reserved.variables.request.php
 */
class Request
{
    /**
     * Gets/returns the value of a specific key of the POST super-global.
     * When using just Request::post('x') it will return the raw and untouched $_POST['x'], when using it like
     * Request::post('x', true) then it will return a trimmed and stripped $_POST['x'] !
     *
     * @param mixed $key key
     * @param bool $clean marker for optional cleaning of the var
     * @param mixed null or http://php.net/manual/en/filter.filters.sanitize.php
     * @return mixed the key's value or nothing
     */
    public static function post($key, $clean = false, $filter = null)
    {
        if (isset($_POST[$key])) {
            if ($filter !== null) {
                $value = filter_input(INPUT_POST, $key, $filter);
            } else {
                $value = $_POST[$key];
            }
            return ($clean) ? trim(strip_tags($value)) : $value;
        }
    }

    public static function post_debug()
    {
        return print_r($_POST, true);
    }

    /**
     * Returns the state of a checkbox.
     *
     * @param mixed $key key
     * @return mixed state of the checkbox
     */
    public static function postCheckbox($key)
    {
        return isset($_POST[$key]) ? 1 : null;
    }

    /**
     * gets/returns the value of a specific key of the GET super-global
     * @param mixed $key key
     * @param mixed null or http://php.net/manual/en/filter.filters.sanitize.php
     * @return mixed the key's value or nothing
     */
    public static function get($key, $filter = null)
    {
        if (isset($_GET[$key])) {
            if ($filter !== null) {
                return filter_input(INPUT_GET, $key, $filter);
            }
            return $_GET[$key];
        }
    }

    public static function exists($key)
    {
        return (isset($_GET[$key]) || isset($_POST[$key]));
    }

    /**
     * gets/returns the value of a specific key of the GET super-global, except it doesn't escape + to space
     * @param mixed $key key
     * @return mixed the key's value or nothing
     */
    public static function real_get($key)
    {
        if (isset($_GET[$key])) {
            $raw = str_replace(' ', '+', $_GET[$key]); // space is supposed to be plus
            return $raw;
        }
    }

    /**
     * gets/returns the value of a specific key of the COOKIE super-global
     * @param mixed $key key
     * @return mixed the key's value or nothing
     */
    public static function cookie($key)
    {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
    }
}
