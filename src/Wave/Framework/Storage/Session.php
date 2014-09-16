<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 12:34
 */

namespace Wave\Framework\Storage;


use Wave\Framework\Decorator\Decoratable;

class Session extends Decoratable
{
    protected $sessionName = 'x_session_id';
    protected $uid = null;

    protected $storage = null;

    public function __construct($name = 'x_session_id')
    {
        $this->sessionName = $name;
        $cookie = new Cookie($this->sessionName);
        if (!$cookie->exists()) {
            $cookie->set(uniqid(null, true));
        }

        $this->storage = new Registry(array(
            'mutable' => true,
            'replace' => true
        ));
    }

    public function getId()
    {
        $cookie = new Cookie($this->sessionName);

        if (!$cookie->exists()) {
            throw new \LogicException("Session isn't instantiated properly.");
        }
        return $cookie->get();
    }

    public function __set($key, $value)
    {
        $this->storage->set($key, $value);
    }

    public function __get($key)
    {
        $cookie = new Cookie($key);
        if (!$this->storage->exists($key) && $cookie->exists()) {
            $value = $this->invokeRollbackDecorators($cookie->get());
            $this->storage->set($key, $value);
            return $value;
        }

        return $this->storage->get($key);
    }

    public function __destruct()
    {
        $iterator = $this->storage->getIterator();
        $iterator->rewind();
        while ($iterator->valid()) {
            $cookie = new Cookie($iterator->key());
            $cookie->set($this->invokeCommitDecorators($iterator->current()));
            $iterator->next();
        }
    }
}