<?php

namespace Sim\Csrf\Storage;

use Sim\Csrf\Utils\ArrayUtil;
use Sim\Csrf\Utils\CsrfUtil;

class CsrfSessionStorage implements ICsrfStorage
{
    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $time): ICsrfStorage
    {
        CsrfUtil::setTimesSession($key, $value, $time);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return CsrfUtil::getTimedSession($key);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key): bool
    {
        return CsrfUtil::hasTimedSession($key);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key): ICsrfStorage
    {
        CsrfUtil::removeTimedSession($key);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function extend($key, int $expiration): ICsrfStorage
    {
        if (CsrfUtil::hasTimedSession($key)) {
            $res = ArrayUtil::get($_SESSION, $key);
            if (is_array($res) && (isset($res['ttl']) && time() <= $res['ttl'])) {
                if (!empty($expiration) && (int)$expiration > 0) {
                    $token = $res['data'] ?? $res;
                    if ($token && isset($res['ttl'])) {
                        $this->set($key, $token, $expiration);
                    }
                }
            }
        } else {
            $token = null;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clear($prefix): ICsrfStorage
    {
        if (is_string($prefix)) {
            CsrfUtil::removeTimedSession($prefix);
            $_SESSION[$prefix] = [];
        }
        return $this;
    }
}