<?php

namespace BeeJeeTest\Core\Helper;

use ParagonIE\AntiCSRF\AntiCSRF;

class FormSecurity {

    protected $_csrf;

    public function __get($property)
	{
		if ($property == 'csrf')
		{
            if (!isset($this->_csrf))
            {
                $this->_csrf = new AntiCSRF();
                $this->_csrf->reconfigure([ 'hmac_ip' => false ]);
            }

			return $this->_csrf;
        }
        else
        {
            throw new \Exception("Property {$property} doesn't exist.");
        }
	}

    public function token($lockTo = '')
    {
        echo $this->csrf->insertToken($lockTo, false);
    }

}
