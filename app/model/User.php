<?php

namespace BeeJeeTest\Model;

use BeeJeeTest\Core\Model;

class User extends Model {

    public static function find($login)
    {
        $user = null;

        if ($login == 'admin')
        {
            // TODO: Use database here

            $user = new User();
            $user->data = [
                'login' => 'admin',
                'password' => self::hashPassword('123'),
            ];
        }

        return $user;
    }

    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword($password)
    {
        return password_verify($password, $this->data['password']);
    }

}
