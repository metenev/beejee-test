<?php

namespace BeeJeeTest\Core;

use ParagonIE\AntiCSRF\AntiCSRF;

class Request {

	const METHOD_GET = 'get';
	const METHOD_POST = 'post';

	const ALLOWED_PROPERTIES = [ 'scheme', 'host', 'uri' ];

	protected $_get;
	protected $_post;

	protected $scheme;
	protected $host;
	protected $uri;
	protected $method;

	protected $dangerousMethods;

	protected $csrf;

	public function __get($property)
	{
		if (in_array($property, self::ALLOWED_PROPERTIES))
		{
			return $this->$property;
		}
		else
		{
			throw new \Exception("Property {$property} doesn't exist.");
		}
	}

	public function __construct($method, $scheme, $host, $path)
	{
		$this->method = $method;
		$this->scheme = $scheme;
		$this->host = $host;
		$this->uri = $path;

		// TODO: Can do some sanitizing here
		$this->_get = $this->sanitize($_GET);
		$this->_post = $this->sanitize($_POST);

		$this->dangerousMethods = [
			self::METHOD_POST,
			// PUT, PATCH, DELETE
		];
	}

	public function getUri($full = false)
	{
		$result = $this->uri;

		$root = getenv('ROOT_PATH');

		if (!empty($root))
		{
			$result = '/' . $root . '/' . $result;
        }

		if ($full)
		{
			$result = "{$this->scheme}://{$this->host}/" . trim($result, '/');
		}

		return $result;
	}

	public function method($guess = null)
	{
		if (!isset($guess)) return $this->method;

		return $this->method == $guess;
	}

	public function get($field = null, $default = null)
	{
		if (isset($field))
		{
			return isset($this->_get[ $field ]) ? $this->_get[ $field ] : $default;
		}
		else return $this->_get;
	}

	public function post($field = null, $default = null)
	{
		if (isset($field))
		{
			return isset($this->_post[ $field ]) ? $this->_post[ $field ] : $default;
		}
		else return $this->_post;
	}

	public function isValid()
	{
		if (in_array($this->method, $this->dangerousMethods))
		{
			if (!isset($this->csrf))
			{
				$this->csrf = new AntiCSRF();
				$this->csrf->reconfigure([ 'hmac_ip' => false ]);
			}

			if (!$this->csrf->validateRequest())
			{
				// TODO: Log a CSRF attack attempt

				return false;
			}
		}

		return true;
	}

	protected function sanitize(array $data)
	{
		$result = [];

		foreach ($data as $key => $value)
		{
			if (is_array($value))
			{
				$result[ $key ] = $this->sanitize($value);
			}
			else
			{
				$result[ $key ] = htmlspecialchars($value, ENT_QUOTES);
			}
		}

		return $result;
	}

}
