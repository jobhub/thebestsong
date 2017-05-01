<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
	protected $_isJsonResponse = false;

	  // Call this func to set json response enabled
	  public function setJsonResponse() {
		$this->view->disable();
		
		$this->_isJsonResponse = true;
		$this->response->setContentType('application/json', 'UTF-8');
	  }

	  // After route executed event
	  public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if ($this->_isJsonResponse) {
		  $data = $dispatcher->getReturnedValue();
		  // if (is_array($data)) {
			$data = json_encode($data);
		  // }

		  $this->response->setContent($data);
		  
		  return $this->response->send();
		}
	  }
  
    protected function initialize()
    {
        Phalcon\Tag::prependTitle('TBS | ');

		/* Disables layout if request is made via ajax */ 
		if ($this->request->isAjax()) { 
			$this->setJsonResponse(); 
		}
    }
 
    protected function forward($uri) {
    	$uriParts = explode('/', $uri);
    	return $this->dispatcher->forward(
    		array(
    			'controller' => $uriParts[0], 
    			'action' => $uriParts[1]
    		)
    	);
    }
	
	protected function checkSession() {
        $client_session_key = $this->request->getPost("client_session_key");        
        $session_key 		= $this->request->getPost("session_key");
        $user_id 			= $this->request->getPost("user_id");
        
        $where = "session_key = :session_key: AND client_session_key = :client_session_key: AND user_id = :user_id: ";
        
        $session = UserSession::findFirst(array($where, 
        "bind" => array ("session_key" => $session_key, "client_session_key" => $client_session_key, "user_id" => $user_id)));
		
        if (!$session) {
            return false;
            // throw new Exception("Not a valid session !");
        } else {
			$this->auth->authUserById($user_id);
            return $session;
        }
	}
}
