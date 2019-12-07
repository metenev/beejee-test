<?php

namespace BeeJeeTest\Core;

class Session {

    public function start()
    {
        session_name('session_id');
        session_start();
    }

    public function has($field)
    {
        return isset($_SESSION[ $field ]);
    }

    public function get($field, $default = null)
    {
        return $this->has($field) ? $_SESSION[ $field ] : $default;
    }

    public function set($field, $value)
    {
        $_SESSION[ $field ] = $value;
    }

    public function remove($field)
    {
        unset($_SESSION[ $field ]);
    }

}