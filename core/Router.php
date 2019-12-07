<?php

namespace BeeJeeTest\Core;

use BeeJeeTest\Core\Request;
use BeeJeeTest\Core\Session;

class Router {

	protected $rules;

	public function __construct()
	{
		$this->rules = [];
	}

	public function rule($path, array $params)
	{
		$path = trim(trim($path), '/');

		if (isset($this->rules[ $path ]))
		{
			throw new \Exception("Overriding of the rule '{$path}'");
		}

		$this->rules[ $path ] = $params;
	}

	public function hasRule($path, $trim = true)
	{
		if ($trim)
		{
			$path = trim(trim($path), '/');
		}

		return isset($this->rules[ $path ]);
	}

	public function getRule($path)
	{
		$path = trim(trim($path), '/');

		return $this->hasRule($path, false)
			? $this->rules[ $path ]
			: null;
	}

	public function process()
	{
		$routeConfig = [
			'controller' => 'Index',
			'action' => 'index'
		];

		$path = trim($_SERVER['REQUEST_URI'], '/');

		$query = null;
		$question = strpos($path, '?');

		if ($question !== false)
		{
			$query = substr($path, $question + 1);
			$path = trim(substr($path, 0, $question), '/');
		}

		$root = getenv('ROOT_PATH');

		if (!empty($root))
		{
			$path = preg_replace('#^' . getenv('ROOT_PATH') . '#', '', $path);
			$path = trim($path, '/');
		}

		// Check if whole path is defined as rule

		if ($this->hasRule($path))
		{
			$routeConfig = array_merge($routeConfig, $this->getRule($path));
			$this->processRoute($routeConfig, $path);
			return;
		}

		// Then maybe this is a controller with an action

		if (!empty($path))
		{
			$routeParts = explode('/', $path);

			$lastPart = array_pop($routeParts);

			if (!empty($routeParts)) $routeConfig['controller'] = implode('/', $routeParts);
			$routeConfig['action'] = $lastPart;
		}

		$this->processRoute($routeConfig, $path);
	}

	protected function processRoute(array $config, $path)
	{
		$controllerName = $config['controller'];
		$actionName = $config['action'];

		// Include controller

		$controllerParts = $this->makeUnifiedPathArray($controllerName);
		$controllerPath = $this->makeUnifiedPathFromUnifiedArray($controllerParts, PATH_APP . 'controller/');

		if (!file_exists($controllerPath))
		{
			$this->errorPage404();
			return;
		}

		// Create request object

		$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
		$host = $_SERVER['HTTP_HOST'];
		$method = strtolower($_SERVER['REQUEST_METHOD']);

		$request = new Request($method, $scheme, $host, $path);

		// Create session

		$session = new Session();
		$session->start();

		// Create controller

		$controllerName = $this->makeNameFromUnifiedArray($controllerParts, 'Controller');
		$controller = new $controllerName($request, $session);

		// Include model

		// $modelName = $controllerName;

		// $modelParts = $this->makeUnifiedPathArray($modelName);
		// $modelPath = $this->makeUnifiedPathFromUnifiedArray($modelParts, PATH_APP . 'model/');

		// if (file_exists($modelPath))
		// {
		// 	$modelName = $this->makeNameFromUnifiedArray($modelParts, PATH_APP . 'Model');
		// 	$model = new $modelName();
		// }

		// Prepare

		$controller->prepare();

		// Execute action

		$actionName = 'action_' . $actionName;

		if (method_exists($controller, $actionName))
		{
			$controller->$actionName();
		}
		else
		{
			$this->errorPage404();
		}
	}

	protected function makeUnifiedPathArray($path)
	{
		$parts = explode('/', $path);

		return array_map('ucfirst', $parts);
	}

	protected function makeUnifiedPathFromUnifiedArray(array $parts, $root = PATH_ROOT)
	{
		return $root . implode('/', $parts) . '.php';
	}

	protected function makeNameFromUnifiedArray(array $parts, $namespace = null)
	{
		return '\\BeeJeeTest\\' . (isset($namespace) ? $namespace . '\\' : '') . implode('\\', $parts);
	}

	protected function errorPage404()
	{
		$host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
		header('HTTP/1.1 404 Not Found');
		header('Status: 404 Not Found');
		// header('Location:' . $host . '404');
	}
}
