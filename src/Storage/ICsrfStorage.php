<?php

namespace Sim\Csrf\Storage;

interface ICsrfStorage
{
    /**
     * @param $key
     * @param $value
     * @param $time
     * @return ICsrfStorage
     */
    public function set($key, $value, $time): ICsrfStorage;

    /**
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @return bool
     */
    public function has($key): bool;

    /**
     * @param $key
     * @return ICsrfStorage
     */
    public function remove($key): ICsrfStorage;

    /**
     * @param $key
     * @param int $expiration
     * @return ICsrfStorage
     */
    public function extend($key, int $expiration): ICsrfStorage;

    /**
     * Remove all stored tokens
     *
     * @param string $prefix
     * @return ICsrfStorage
     */
    public function clear($prefix): ICsrfStorage;
}