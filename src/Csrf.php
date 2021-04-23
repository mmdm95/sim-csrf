<?php

namespace Sim\Csrf;

use Sim\Csrf\Storage\CsrfSessionStorage;
use Sim\Csrf\Storage\ICsrfStorage;
use Sim\Csrf\Utils\CsrfUtil;

class Csrf implements ICsrf
{
    /**
     * Storage types
     */
    const STORAGE_TYPE_SESSION = 1;
    const STORAGE_TYPE_CUSTOM = 2;

    /**
     * @var int
     */
    protected $timeout = 300;

    /**
     * @var string
     */
    protected $default_name = 'default';

    /**
     * @var string
     */
    protected $input_name = 'csrftoken';

    /**
     * @var string
     */
    protected $token_session_name = '__simplicity_csrf_tokens_';

    /**
     * @var ICsrfStorage
     */
    protected $storage;

    /**
     * @var bool
     */
    protected $extend_timeout = true;

    /**
     * Csrf constructor.
     * @param ICsrfStorage|null $storage
     */
    public function __construct(ICsrfStorage $storage = null)
    {
        if (!is_null($storage)) {
            $this->storage = $storage;
        } else {
            $this->storage = new CsrfSessionStorage();
        }
        $this->init();
    }

    /**
     * {@inheritdoc}
     */
    public function setStorage(ICsrfStorage $storage): ICsrf
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorage(): ICsrfStorage
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getExpiration(): int
    {
        return $this->timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function extendExpiration(bool $answer = true)
    {
        $this->extend_timeout = $answer;
        return $this;
    }


    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function getField(?string $name = null, ?string $input_name = null): string
    {
        $token = $this->getToken($name);
        $input_name = $input_name ?? $this->input_name;
        return '<input type="hidden" name="' . $input_name . '" value="' . $token . '">';
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function getToken(?string $name = null): string
    {
        $name = $name ?? $this->default_name;
        $hashed = $this->hashName($name);
        if ($this->storage->has($this->_dotConcatenation($this->token_session_name, $hashed))) {
            $token = $this->storage->get($this->_dotConcatenation($this->token_session_name, $hashed), $this->timeout);
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
     * {@inheritdoc}
     * @throws \Exception
     */
    public function regenerateToken(?string $name = null): string
    {
        $name = $name ?? $this->default_name;
        $hashed = $this->hashName($name);
        $token = $this->generateToken();
        $this->storage->set($this->_dotConcatenation($this->token_session_name, $hashed), $token, $this->timeout);
        return $token;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function validate($token, $name = null): bool
    {
        $ownToken = $this->getToken($name);
        $res = !is_null($token) && $ownToken === $token;
        $hashed = $this->hashName($name);
        if ($res && $this->extend_timeout) {
            $this->storage->extend($this->_dotConcatenation($this->token_session_name, $hashed), $this->timeout);
        }
        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): ICsrf
    {
        $this->storage->clear($this->token_session_name);
        return $this;
    }

    /**
     * Initialize needed functionality
     */
    protected function init()
    {
        if ($this->storage instanceof CsrfSessionStorage) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION[$this->token_session_name])) {
                $_SESSION[$this->token_session_name] = [];
            }
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
        $token = uniqid(str_shuffle(substr($token, random_int(0, strlen($token) - 7), 6)), true);
        return base64_encode(str_shuffle($token));
    }

    /**
     * Hash a given name
     *
     * @param string|null $name
     * @return string
     */
    protected function hashName(?string $name = null)
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
