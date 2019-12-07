<?php

namespace BeeJeeTest\Controller;

use BeeJeeTest\Core\Controller;
use BeeJeeTest\Core\Request;
use BeeJeeTest\Core\View;
use BeeJeeTest\Core\Validation;
use BeeJeeTest\Core\Helper\Url;
use BeeJeeTest\Core\Helper\FormSecurity;
use BeeJeeTest\Model\Task;
use BeeJeeTest\Helper\FormAlerts;
use BeeJeeTest\Helper\Pagination;

class Index extends Controller {

	private $orderFields;
	private $orderDirections;

	private $scripts;
	private $styles;

	private $url;

	private $template;

	public function prepare()
	{
		parent::prepare();

		$this->orderFields = [
			'user' => [ 'key' => 'user', 'name' => 'User', 'db_column' => 'user_name' ],
			'email' => [ 'key' => 'email', 'name' => 'E-mail', 'db_column' => 'user_email' ],
			'status' => [ 'key' => 'status', 'name' => 'Status', 'db_column' => 'status' ],
			'created' => [ 'key' => 'created', 'name' => 'Created date', 'db_column' => 'created_at' ],
		];

		$this->orderDirections = [
			'asc' => [ 'key' => 'asc', 'name' => 'Ascending', 'db_symbol' => 'ASC' ],
			'desc' => [ 'key' => 'desc', 'name' => 'Descending', 'db_symbol' => 'DESC' ],
		];

		$this->scripts = [
			'js/tasks.js',
		];
		$this->styles = [
			'css/tasks.css',
		];

		$this->url = new Url();

		$this->template = new View('Template');

		$this->template->bind('styles', $this->styles);
		$this->template->bind('scripts', $this->scripts);

		$this->template->set([
			'user' => $this->session->get('user'),
			'url' => $this->url,
		]);
	}

	public function action_index()
	{
		$alerts = new FormAlerts();
		$alerts->fillFromSession($this->session);

		// Order settings

		$selectedOrderField = $this->request->get('sort');

		if (isset($selectedOrderField))
		{
			$selectedOrderDir = $this->request->get('dir', 'asc');
		}
		else
		{
			// Default sorting is by created time DESC

			$selectedOrderField = 'created';
			$selectedOrderDir = $this->request->get('dir', 'desc');
		}

		// Pagination settings

		$pageSize = getenv('TASKS_PAGE_SIZE');
		$page = max($this->request->get('page', 1), 1) - 1;

		// Get tasks

		$tasksCount = Task::count();

		$tasks = Task::getList([
			// We map frontend fields to database fields here
			'order' => $this->getOrderParams($selectedOrderField, $selectedOrderDir),
			'offset' => [
				'from' => $page * $pageSize,
				'limit' => $pageSize,
			]
		]);

		$pagesCount = ceil($tasksCount / $pageSize);

		// Render view

		$content = new View('Index');

		$content->set([
			'url' => $this->url,
			'user' => $this->session->get('user'),
			'alerts' => $alerts,
			'tasks' => $tasks,
			'page' => $page,
			'pagesCount' => $pagesCount,
			'pagination' => new Pagination($this->request),
			'orderFields' => $this->orderFields,
			'orderDirections' => $this->orderDirections,
			'selectedOrderField' => $selectedOrderField,
			'selectedOrderDir' => $selectedOrderDir,
		]);

		$this->template->set('content', $content->render());

		echo $this->template->render();
	}

	public function action_create()
	{
		$alerts = new FormAlerts();

		if (!$this->validateJSONRequest($alerts))
		{
			$this->respondJSONErrors($alerts->getJSON());
			return;
		}

		$formSecurity = new FormSecurity();

		// Validate request fields

		if (!Validation::notEmpty($this->request->post('user_name')))
		{
			$alerts->add("User name should be set", FormAlerts::TYPE_ERROR, 'user_name');
		}
		else if (!Validation::length($this->request->post('user_name'), 30, 3))
		{
			$alerts->add("Name should be between 3 and 30 characters", FormAlerts::TYPE_ERROR, 'user_name');
		}

		if (!Validation::notEmpty($this->request->post('user_email')))
		{
			$alerts->add("E-mail should be set", FormAlerts::TYPE_ERROR, 'user_email');
		}
		else if (!Validation::email($this->request->post('user_email')))
		{
			$alerts->add("E-mail is invalid", FormAlerts::TYPE_ERROR, 'user_email');
		}

		if (!Validation::notEmpty($this->request->post('content')))
		{
			$alerts->add("Task content should not be empty", FormAlerts::TYPE_ERROR, 'content');
		}

		if (!$alerts->isEmpty())
		{
			$this->respondJSONErrors(
				$alerts->getJSON(),
				[ // Additional fields
					'csrf' => $formSecurity->csrf->getTokenArray($this->url->get('/index/create')),
				]
			);
			return;
		}

		// Create task

		try
		{
			$task = Task::create($this->request->post());
		}
		catch (\Exception $e)
		{
			$alerts->add("Could not create task: " . $e->getMessage());

			$this->respondJSONErrors(
				$alerts->getJSON(),
				[ // Additional fields
					'csrf' => $formSecurity->csrf->getTokenArray($this->url->get('/index/create')),
				]
			);
			return;
		}

		// Respond

		$alerts->add("Task successfully created", FormAlerts::TYPE_SUCCESS);
		$alerts->saveToSession($this->session);

		$this->respondJSONData(true);
	}

	public function action_edit()
	{
		$alerts = new FormAlerts();

		if (!$this->validateJSONRequest($alerts))
		{
			$this->respondJSONErrors($alerts->getJSON());
			return;
		}

		$formSecurity = new FormSecurity();

		if (!$this->session->get('user'))
		{
			$alerts->add('Not allowed', FormAlerts::TYPE_ERROR, null, 403);
			$this->respondJSONErrors(
				$alerts->getJSON(),
				[ // Additional fields
					'csrf' => $formSecurity->csrf->getTokenArray($this->url->get('/index/edit')),
				]
			);
			return;
		}

		// Validate request fields

		if (!Validation::notEmpty($this->request->post('id')))
		{
			$alerts->add("Task not found", FormAlerts::TYPE_ERROR);
		}
		else if (!Validation::notEmpty($this->request->post('content')))
		{
			$alerts->add("Task content should not be empty", FormAlerts::TYPE_ERROR, 'content');
		}

		if (!$alerts->isEmpty())
		{
			$this->respondJSONErrors(
				$alerts->getJSON(),
				[ // Additional fields
					'csrf' => $formSecurity->csrf->getTokenArray($this->url->get('/index/edit')),
				]
			);
			return;
		}

		$task = Task::getById($this->request->post('id'));

		if (!$task)
		{
			$alerts->add("Task not found", FormAlerts::TYPE_ERROR);
			$this->respondJSONErrors(
				$alerts->getJSON(),
				[ // Additional fields
					'csrf' => $formSecurity->csrf->getTokenArray($this->url->get('/index/edit')),
				]
			);
			return;
		}

		// Update task

		try
		{
			if ($task->field('content') != $this->request->post('content'))
			{
				$task->set('content', $this->request->post('content'));
				$task->set('edited_by_admin', 1);
			}

			if ($this->request->post('completed') == 1)
			{
				$task->set('status', 'completed');
			}

			$task->save();
		}
		catch (\Exception $e)
		{
			$alerts->add("Could not update task: " . $e->getMessage());

			$this->respondJSONErrors(
				$alerts->getJSON(),
				[ // Additional fields
					'csrf' => $formSecurity->csrf->getTokenArray($this->url->get('/index/edit')),
				]
			);
			return;
		}

		// Respond

		$alerts->add("Task successfully edited", FormAlerts::TYPE_SUCCESS);
		$alerts->saveToSession($this->session);

		$this->respondJSONData(true);
	}

	//-

	private function getOrderParams($field, $dir)
	{
		$resultField = null;
		$resultDir = null;

		if (!isset($this->orderFields[ $field ]))
		{
			throw new \Exception('Invalid sorting field');
		}
		else if (!isset($this->orderDirections[ $dir ]))
		{
			throw new \Exception('Invalid sorting direction');
		}

		$resultField = $this->orderFields[ $field ]['db_column'];
		$resultDir = $this->orderDirections[ $dir ]['db_symbol'];

		return [
			[ 'field' => $resultField, 'dir' => $resultDir ],
		];
	}

	private function validateJSONRequest(FormAlerts &$alerts)
	{
		if (!$this->request->method(Request::METHOD_POST))
		{
			$alerts->add("Invalid request", FormAlerts::TYPE_ERROR);
			return false;
		}
		else if (!$this->request->isValid())
		{
			$alerts->add("Invalid request, please refresh the page", FormAlerts::TYPE_ERROR);
			return false;
		}

		// Further validation here...

		return true;
	}

	private function respondJSON(array $data)
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data);
	}

	private function respondJSONErrors(array $errors, array $additional = null, $statusCode = 400)
	{
		http_response_code($statusCode);

		$response = [
			'status' => 'error',
			'errors' => $errors,
		];

		if (isset($additional))
		{
			$response = array_merge($response, $additional);
		}

		$this->respondJSON($response);
	}

	private function respondJSONData($data, $status = 'ok')
	{
		$this->respondJSON([
			'status' => $status,
			'data' => $data,
		]);
	}

}
