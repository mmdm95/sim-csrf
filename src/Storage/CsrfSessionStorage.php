<?php

namespace Sim\Csrf\Storage;

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
    public function clear($prefix): ICsrfStorage
    {
        if (is_string($prefix)) {
            CsrfUtil::removeTimedSession($prefix);
            $_SESSION[$prefix] = [];
        }
        return $this;
    }
}