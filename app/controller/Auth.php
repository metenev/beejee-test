<?php

namespace BeeJeeTest\Controller;

use BeeJeeTest\Core\Controller;
use BeeJeeTest\Core\View;
use BeeJeeTest\Core\Request;
use BeeJeeTest\Core\Validation;
use BeeJeeTest\Core\Helper\Url;
use BeeJeeTest\Helper\FormAlerts;
use BeeJeeTest\Model\User;

class Auth extends Controller {

    /**
     * Log in action
     */
    public function action_index()
    {
        $user = $this->session->get('user');

        if (isset($user))
        {
            $url = new Url();
            $this->redirect($url->get('/'));
            return;
        }

        $alerts = new FormAlerts();

        if ($this->request->method(Request::METHOD_POST))
        {
            if (!$this->request->isValid())
            {
                $alerts->add("Invalid request, please refresh the page", FormAlerts::TYPE_ERROR);
                $alerts->saveToSession($this->session);
                $this->redirectToSelf();
                return;
            }

            $this->session->set('login-data', [
                'login' => $this->request->post('login'),
            ]);

            // Validate request fields

            if (!Validation::notEmpty($this->request->post('login')))
            {
                $alerts->add("Login is required", FormAlerts::TYPE_ERROR, 'login');
            }
            if (!Validation::notEmpty($this->request->post('password')))
            {
                $alerts->add("Password is required", FormAlerts::TYPE_ERROR, 'password');
            }

            if (!$alerts->isEmpty())
            {
                $alerts->saveToSession($this->session);
                $this->redirectToSelf();
                return;
            }

            // Find user

            $user = User::find($this->request->post('login'));

            if (!isset($user))
            {
                $alerts->add("Wrong email or password", FormAlerts::TYPE_ERROR);
                $alerts->saveToSession($this->session);
                $this->redirectToSelf();
                return;
            }

            // Check user credentials

            if (!$user->verifyPassword($this->request->post('password')))
            {
                $alerts->add("Wrong email or password", FormAlerts::TYPE_ERROR);
                $alerts->saveToSession($this->session);
                $this->redirectToSelf();
                return;
            }

            // User is OK

            $this->session->set('user', $user);
            $url = new Url();
            $this->redirect($url->get('/'));
            return;
        }
        else
        {
            // This happens on all requests other than POST

            $alerts->fillFromSession($this->session);

            $template = new View('Auth/Template');

            $content = new View('Auth/Login');
            $content->set([
                'alerts' => $alerts,
                'data' => $this->session->get('login-data'),
            ]);

            $this->session->remove('login-data');

            $template->set('content', $content->render());

            echo $template->render();
        }
    }

    /**
     * Log out action
     */
    public function action_logout()
    {
        $this->session->remove('user');
        $url = new Url();
        $this->redirect($url->get('/'));
    }

}
