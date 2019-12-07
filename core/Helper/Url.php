<?php

namespace BeeJeeTest\Core\Helper;

class Url {

    public function get($path = null)
    {
        $result = isset($path) ? $path : '/';

        $root = getenv('ROOT_PATH');

		if (!empty($root))
		{
			$result = $root . '/' . trim($result, '/');
        }

        $result = '/' . $result;

		return $result;
    }

}
