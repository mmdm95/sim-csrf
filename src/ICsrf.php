<?php

namespace Sim\Csrf;

use Sim\Csrf\Storage\ICsrfStorage;

interface ICsrf
{
    /**
     * @param ICsrfStorage $storage
     * @return ICsrf
     */
    public function setStorage(ICsrfStorage $storage): ICsrf;

    /**
     * @return ICsrfStorage
     */
    public function getStorage(): ICsrfStorage;

    /**
     * Set ttl for CSRF token
     *
     * @param int $timeout - in seconds
     * @return ICsrf
     */
    public function setExpiration(int $timeout): ICsrf;

    /**
     * Get ttl of CSRF token
     *
     * @return int
     */
    public function getExpiration(): int;

    /**
     * Extend expiration ttl or not
     *
     * @param bool $answer
     * @return static
     */
    public function extendExpiration(bool $answer = true);

    /**
     * Get hidden HTML input element for CSRF field
     *
     * @param string|null $name
     * @param string|null $input_name
     * @return string
     */
    public function getField(?string $name = null, ?string $input_name = null): string;

    /**
     * Just give CSRF token
     *
     * @param string|null $name
     * @return string
     */
    public function getToken(?string $name = null): string;

    /**
     * Regenerate Token
     *
     * @param string|null $name
     * @return string
     */
    public function regenerateToken(?string $name = null): string;

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