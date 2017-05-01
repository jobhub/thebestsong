<?php

use Phalcon\Tag as Tag;

// use TBS\Models\User;

class SessionController extends ControllerBase {

    public function initialize() {
        parent::initialize();
        // $this->view->setTemplateAfter('main');
        Tag::setTitle('Sign Up/Sign In');
    }

    public function indexAction() {

        // check session 
        echo "session Id : " . $this->session->getId() . "<br>";

        if ($this->session->has("auth_identity")) {
            echo "Logged in " . PHP_EOL;
            print_r($this->session->get("auth_identity"));
        } else
            echo "Please log in ";
    }

    // TO DO : rewrite code using Phalcon validation 
    public function registerAction() {
        $request = $this->request;
        $error = false;
        $result = new stdClass;


        if ($request->isPost()) {
            $fields = array("name", "email", "password");
			// do we need striptags ?
            $name 	= $request->getPost('name', array('string', 'striptags'));
            $email 	= $request->getPost('email', 'email');
            $password = $request->getPost('password');
            $pin 	= $request->getPost('pin');

            $user = new User();
            // $user->login = $email;
            $user->email = $email;
            $user->password = $this->security->hash($password);
            $user->name = $name;

            $user->registration_date = new Phalcon\Db\RawValue('now()');
            $user->active = 'N';
            $user->profile_id = 1;

            if ($user->create() == false) {
                foreach ($user->getMessages() as $message) {
                    $result->error .= (string) $message . PHP_EOL;
                }
            } else {
                $result->success = 'Thanks for sign-up, please log-in';
            }
        }
        print_r2($result);
        return $result;
    }

    private function returnResponse($response) {

        $this->view->disable();

        //Create a response instance
        $response = new \Phalcon\Http\Response();

        //Set the content of the response
        $response->setContent(json_encode($response));

        //Return the response
        return $response->send();
    }

    /**
     * Register authenticated user into session data
     * and save user data to User_Session table
     * @param Users $user
     * @param bool $rememberMe
     * 
     * @return bool 
     */
    private function _registerSession($user, $rememberMe) {
        if ($rememberMe) {
            $lifetime = 600;
            session_set_cookie_params($lifetime);

            $this->session->destroy();
            $this->session->start();
        }

        $this->session->set('auth', array(
            'id' => $user->id,
            'name' => $user->name
        ));
        // keep error messages as session bag
        $error = new Phalcon\Session\Bag('error');

        $ses_id = $this->session->getId();
        if (empty($ses_id))
            $this->session->start();

        if (!$this->session->has("safety")) {
            session_regenerate_id(true);
            $this->session->set("safety", true);
        }

        $ses_id = $this->session->getId();
        // save data to User_Session table 
        $user_session = new UserSession();

        $user_session->session_id = $ses_id;
        $user_session->user_id = $user->id;

        $user_session->start_time = new Phalcon\Db\RawValue('now()');
        // this should be set when the user logs out or the session ends
        // $user_session->end_time = ...;

        $user_session->remember_me = $rememberMe;
        $user_session->ip = $this->request->getClientAddress();

        if (!$user_session->create()) {
            foreach ($user_session->getMessages() as $message) {
                $error->message .= $message . PHP_EOL;
            }

            return false;
        }

        return true;
    }

    /**
     * This actions receive the input from the login form
     *
     */
    public function loginAction() {
        try {
            if ($this->request->isPost()) {
                // validate login form 
                // try to  login 
                $this->auth->check(array(
                    'login' => $this->request->getPost('login'),
                    'password' => $this->request->getPost('password'),
                    'remember' => $this->request->getPost('remember')
                ));
            } else {
                if ($this->auth->hasRememberMe()) {
                    return $this->auth->loginWithRememberMe();
                }
            }

            $result = array("success" => "Hello " . $this->auth->getName());
            return $result;
        } catch (Exception $e) {

            if ($this->request->isAjax()) {
                $result = array("error" => $e->getMessage());
                return $result;
            } else {
                $this->flash->error($e->getMessage());
            }
        }

        // suggestions 
        // $this->suggestions->update();
        // return $this->forward('session/index');
    }

    /**
     * Finishes the active session redirecting to the index
     *
     * @return unknown
     */
    public function logoutAction() {
        $this->auth->remove();
        // update User_Session
        $sql = "UPDATE User_Session SET end_time = NOW() WHERE session_id = ?";
        $this->db->query($sql, array($this->session->getId()));
        $this->flash->success('Goodbye!');

        // If it's desired to kill the session, also delete the session cookie.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
            );
        }

        $this->session->destroy();

        return $this->forward('session/index');
    }

	// check in User_Session if there is a session with these values 
    public function checkAction() {
        $this->setJSONResponse();

        $client_session_key = $this->request->getPost("client_session_key");        
        $session_key 		= $this->request->getPost("session_key");
        $user_id 			= $this->request->getPost("user_id");
        
        $query = "SELECT *  FROM UserSession 
				  WHERE session_key = :session_key: AND client_session_key = :client_session_key: AND user_id = :user_id: ";

        // $session = $this->modelsManager->executeQuery(array($query, "bind" => array ("session_key" => $session_key, "client_session_key" => $client_session_key, "user_id" => $user_id)));
        $session 	= $this->modelsManager->executeQuery($query, array("session_key" => $session_key, "client_session_key" => $client_session_key, "user_id" => $user_id));

        if (!$session) {
            return array("success" => 0);
        } else {
            return array("success" => 1, "session" => $session->toArray());
        }
    }

}
