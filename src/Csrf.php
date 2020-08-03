<?php

namespace Sim\Csrf;

use Sim\Csrf\Utils\CsrfUtil;

class Csrf implements ICsrf
{
    /**
     * @var int $timeout
     */
    protected $timeout = 300;

    /**
     * @var string $default_name
     */
    protected $default_name = 'default';

    /**
     * @var string $input_name
     */
    protected $input_name = 'csrftoken';

    /**
     * @var string $token_session_name
     */
    protected $token_session_name = '__simplicity_csrf_tokens_';

    /**
     * Csrf constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Set ttl for CSRF token
     *
     * @param int $timeout - in seconds
     * @return Csrf
     */
    public function setExpiration(int $timeout): ICsrf
    {
        if (CsrfUtil::isValidTimestamp($timeout)) {
            $this->timeout = $timeout;
        } else {
            $this->timeout = PHP_INT_MAX;
        }
        return $this;
    }

    /**
     * Get hidden HTML input element for CSRF field
     *
     * @param string|null $name
     * @param string|null $input_name
     * @return mixed
     * @throws \Exception
     */
    public function getField(string $name = null, string $input_name = null)
    {
        $token = $this->getToken($name);
        $input_name = $input_name ?? $this->input_name;
        return '<input type="hidden" name="' . $input_name . '" value="' . $token . '">';
    }

    /**
     * Just give CSRF token
     *
     * @param string|null $name
     * @return mixed
     * @throws \Exception
     */
    public function getToken(string $name = null)
    {
        $name = $name ?? $this->default_name;
        $hashed = $this->hashName($name);
        if (CsrfUtil::hasTimedSession($this->_dotConcatenation($this->token_session_name, $hashed))) {
            $token = CsrfUtil::getTimedSession($this->_dotConcatenation($this->token_session_name, $hashed));
            // alternative of no token in previous access
            if (is_null($token) || empty($token)) {
                $token = $this->regenerateToken($name);
            }
        } else {
            $token = $this->regenerateToken($name);
        }
        return $token;
    }

    /**
     * Regenerate Token
     *
     * @param string|null $name
     * @return mixed
     * @throws \Exception
     */
    public function regenerateToken(string $name = null)
    {
        $name = $name ?? $this->default_name;
        $hashed = $this->hashName($name);
        $token = $this->generateToken();
        CsrfUtil::setTimesSession($this->_dotConcatenation($this->token_session_name, $hashed), $token, $this->timeout);
        return $token;
    }

    /**
     * Validate a token with optional name
     *
     * @param $token
     * @param null $name
     * @return bool
     * @throws \Exception
     */
    public function validate($token, $name = null): bool
    {
        $ownToken = $this->getToken($name);
        return !is_null($token) && $ownToken === $token;
    }

    /**
     * Clear all generated tokens
     *
     * @return ICsrf
     */
    public function clear(): ICsrf
    {
        $_SESSION[$this->token_session_name] = [];
        return $this;
    }

    /**
     * Initialize needed functionality
     */
    protected function init()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION[$this->token_session_name])) {
            $_SESSION[$this->token_session_name] = [];
        }
    }

    /**
     * Generate a token
     *
     * @return string
     * @throws \Exception
     */
    protected function generateToken()
    {
        $length = 64;

        if (function_exists('random_bytes')) {
            return base64_encode(random_bytes($length));
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return base64_encode(openssl_random_pseudo_bytes($length));
        }

        if (function_exists('password_hash')) {
            // We're using a cost of only 6 since it's not a matter
            // of having a strong hash, but rather a random token
            return base64_encode(password_hash(uniqid(), PASSWORD_DEFAULT, [
                'cost' => 6
            ]));
        }

        $token = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = uniqid(substr($token, random_int(0, strlen($token) - 7), 6), true);
        return base64_encode(str_shuffle($token));
    }

    /**
     * Hash a given name
     *
     * @param string|null $name
     * @return string
     */
    protected function hashName($name = null)
    {
        return sha1(strtolower($name ?? $this->default_name));
    }

    /**
     * Concat two strings with dot
     *
     * @param $str1
     * @param $str2
     * @return string
     */
    private function _dotConcatenation($str1, $str2)
    {
        return $str1 . '.' . $str2;
    }
}
