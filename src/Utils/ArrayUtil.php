<?php

namespace Sim\Csrf\Utils;

class ArrayUtil
{
    /**
     * @param $array
     * @param $key
     * @param mixed|null $value
     */
    public static function set(&$array, $key, $value = null)
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
    }

    /**
     * @param $array
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public static function get(&$array, $key, $default = null)
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key])) {
                return $default;
            }
            $array = &$array[$key];
        }
        return $array[array_shift($keys)] ?? $default;
    }

    /**
     * @param $array
     * @param $key
     */
    public static function remove(&$array, $key)
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key])) {
                return;
            }
            $array = &$array[$key];
        }
        unset($array[array_shift($keys)]);
    }

    /**
     * @param $array
     * @param $key
     * @param bool $is_null_ok
     * @return bool
     */
    public static function has(&$array, $key, $is_null_ok = true): bool
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key])) {
                return false;
            }
            $array = &$array[$key];
        }
        $result = true;
        $last = array_shift($keys);
        if (!isset($array[$last])) $result = false;
        if ((bool)$is_null_ok && isset($array[$last]) && is_null($array[$last])) {
            $result = true;
        }
        return $result;
    }
}