<?php

namespace Sim\Csrf\Utils;

class CsrfUtil
{
    /**
     * @param $key
     * @param $value
     * @param $time
     */
    public static function setTimesSession($key, $value, $time)
    {
        $arr = [
            'data' => $value,
            'ttl' => time() + $time,
        ];
        ArrayUtil::set($_SESSION, $key, $arr);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public static function getTimedSession($key)
    {
        if (self::hasTimedSession($key)) {
            $res = ArrayUtil::get($_SESSION, $key);
            if (!is_array($res) || (isset($res['ttl']) && time() > $res['ttl'])) {
                self::removeTimedSession($key);
                return null;
            }
            return $res['data'] ?? $res;
        }
        return null;
    }

    /**
     * @param $key
     * @return bool
     */
    public static function hasTimedSession($key)
    {
        return ArrayUtil::has($_SESSION, $key, false);
    }

    /**
     * @param $key
     */
    public static function removeTimedSession($key)
    {
        ArrayUtil::remove($_SESSION, $key);
    }

    /**
     * @param $timestamp
     * @return bool
     */
    public static function isValidTimestamp($timestamp): bool
    {
        return ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }
}