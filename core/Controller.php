<?php

namespace BeeJeeTest\Core;

use BeeJeeTest\Core\Request;
use BeeJeeTest\Core\Session;

class Controller {

	protected $session;
	protected $request;

	public function __construct(Request $request, Session $session)
	{
		$this->session = $session;
		$this->request = $request;
	}

	public function prepare()
	{
		// Empty for now
	}

	protected function redirect($url, $status = 302)
	{
		header('Location: ' . $url, true, $status);
	}

	protected function redirectToSelf($status = 302)
	{
		header('Location: ' . $this->request->getUri(), true, $status);
	}

}
