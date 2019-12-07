<?php

namespace BeeJeeTest\Core;

use BeeJeeTest\Core\Helper\FormSecurity;

class View {

	protected $viewFile;
	protected $context;

	public function __construct($view)
	{
		$this->viewFile = PATH_APP . 'view/' . $view . '.php';
		$this->context = [
			'formSecurity' => new FormSecurity(),
		];
	}

	public function set($name, $value = null)
	{
		if (is_array($name))
		{
			foreach ($name as $key => $nameValue)
			{
				$this->context[ $key ] = $nameValue;
			}
		}
		else
		{
			$this->context[ $name ] = $value;
		}
	}

	public function bind($name, &$value)
	{
		$this->context[ $name ] =& $value;
	}

	public function render()
	{
		if (!file_exists($this->viewFile)) throw new \Exception("View file {$this->viewFile} is not exists");

		extract($this->context);

		ob_start();

		include $this->viewFile;

		return ob_get_clean();
	}
}
