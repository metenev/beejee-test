<?php

namespace BeeJeeTest\Core;

class Validation {

    public function email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function length($value, $max, $min = NULL)
    {
        $length = mb_strlen($value);

        $result = $length <= $max;
        if (isset($min)) $result = $result && ($length >= $min);

        return $result;
    }

    public function notEmpty($value, $preserveSpace = false)
    {
        if (!$preserveSpace) $value = trim($value);

        return !empty($value);
    }

}
