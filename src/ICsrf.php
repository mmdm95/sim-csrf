<?php

namespace Sim\Csrf;

interface ICsrf
{
    /**
     * Set ttl for CSRF token
     *
     * @param int $timeout - in seconds
     * @return ICsrf
     */
    public function setExpiration(int $timeout): ICsrf;

    /**
     * Get hidden HTML input element for CSRF field
     *
     * @param string|null $name
     * @param string|null $input_name
     * @return mixed
     */
    public function getField(string $name = null, string $input_name = null);

    /**
     * Just give CSRF token
     *
     * @param string|null $name
     * @return mixed
     */
    public function getToken(string $name = null);

    /**
     * Regenerate Token
     *
     * @param string|null $name
     * @return mixed
     */
    public function regenerateToken(string $name = null);

    /**
     * Validate a token with optional name
     *
     * @param $token
     * @param null $name
     * @return bool
     */
    public function validate($token, $name = null): bool;

    /**
     * Clear all generated tokens
     *
     * @return ICsrf
     */
    public function clear(): ICsrf;
}