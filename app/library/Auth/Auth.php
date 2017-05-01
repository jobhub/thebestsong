<?php
namespace TBS\Auth;

use Phalcon\Mvc\User\Component;
// use TBS\Models\User;
use TBS\Models\RememberTokens;
use TBS\Models\SuccessLogins;
use TBS\Models\FailedLogins;

/**
 * TBS\Auth\Auth
 * Manages Authentication/Identity Management in TBS
 */
class Auth extends Component
{

    /**
     * Check the user credentials
     *
     * @param array $credentials
     * 
     * @return string $session_key
     */
    public function check($credentials)
    {

		// Check if client_session_key is valid 
		
        // Check if the user exist
        $user = \User::findFirstByEmail($credentials['login']);
        if ($user == false) {
            $this->registerUserThrottling(0);
            throw new Exception('Wrong email/password combination');
        }
			
        // Check the password
        if (!$this->security->checkHash($credentials['password'], $user->password)) {
            $this->registerUserThrottling($user->id);
            throw new Exception('Wrong email/password combination');
        }
        // Check if the user was flagged
        $this->checkUserFlags($user);

		// Check he's not already logged in 
/*
		$us = \UserSession::findFirst(array("user_id = " . $user->id, "order" => "id DESC"));

		if ($us && strtotime($us->session_expires) >= time())
			throw new Exception('Already logged in');
*/		
        // Register the successful login
        $this->saveSuccessLogin($user);

		// Register session 
		$session_key  = $this->registerSession($user, $credentials["client_session_key"]);
		
        // Check if the remember me was selected
        if (isset($credentials['remember'])) {
            $this->createRememberEnviroment($user);
        }

        $this->session->set('auth-identity', array(
            'id' => $user->id,
            'name' => $user->name,
            'profile' => isset($user->profile->profile_name) ? $user->profile->profile_name : NULL
        ));
        
        return $session_key;
    }

    /**
     * Register succesfull login in DB
     *
     * @param TBS\Models\Users $user
     */
    public function saveSuccessLogin($user)
    {
        $successLogin = new SuccessLogins();
        $successLogin->usersId = $user->id;
        $successLogin->ipAddress = $this->request->getClientAddress();
        $successLogin->userAgent = $this->request->getUserAgent();
        if (!$successLogin->save()) {
            $messages = $successLogin->getMessages();
            throw new Exception($messages[0]);
        }
    }

	public function registerSession($user, $client_session_key, $fb_token = null, $fb_token_expires = null, $deezer_token = null, $deezer_token_expires = null)  
	{
		$user_session = new \UserSession();
		$user_session->user_id = $user->id;
		$user_session->start_time = new \Phalcon\Db\RawValue('now()');
		$user_session->ip = $this->request->getClientAddress();	
		
		// generate session_key 
		$session_key = generateHash(12);
		
		$user_session->session_expires = new \Phalcon\Db\RawValue("DATE_ADD(NOW(), INTERVAL 2 HOUR)");
		
		$user_session->session_key = $session_key;
		$user_session->client_session_key = $client_session_key;
		
		$user_session->fb_token 		= $fb_token;
		$user_session->fb_expires_at 	= $fb_token_expires;
		
		$user_session->deezer_token 			= $deezer_token;
		$user_session->deezer_token_expires 	= $deezer_token_expires;
		
		if (!$user_session->save()) {
			$messages = $user_session->getMessages();
			throw new Exception($messages[0]);			
		}
		
		return $session_key;
	}
	
    /**
     * Implements login throttling
     * Reduces the efectiveness of brute force attacks
     *
     * @param int $userId
     */
    public function registerUserThrottling($userId)
    {
        $failedLogin = new FailedLogins();
        $failedLogin->usersId = $userId;
        $failedLogin->ipAddress = $this->request->getClientAddress();
        $failedLogin->attempted = time();
        $failedLogin->save();
		// temporary hack 		
		return;
		
        $attempts = FailedLogins::count(array(
            'ipAddress = ?0 AND attempted >= ?1',
            'bind' => array(
                $this->request->getClientAddress(),
                time() - 3600 * 6
            )
        ));

        switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
            case 4:
                sleep(2);
                break;
            default:
                sleep(4);
                break;
        }
    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param TBS\Models\Users $user
     */
    public function createRememberEnviroment(User $user)
    {
        $userAgent = $this->request->getUserAgent();
        $token = md5($user->email . $user->password . $userAgent);

        $remember = new RememberTokens();
        $remember->usersId = $user->id;
        $remember->token = $token;
        $remember->userAgent = $userAgent;

        if ($remember->save() != false) {
            $expire = time() + 86400 * 8;
            $this->cookies->set('RMU', $user->id, $expire);
            $this->cookies->set('RMT', $token, $expire);
        }
    }

    /**
     * Check if the session has a remember me cookie
     *
     * @return boolean
     */
    public function hasRememberMe()
    {
        return $this->cookies->has('RMU');
    }

    /**
     * Logs on using the information in the coookies
     *
     * @return Phalcon\Http\Response
     */
    public function loginWithRememberMe()
    {
        $userId = $this->cookies->get('RMU')->getValue();
        $cookieToken = $this->cookies->get('RMT')->getValue();

        $user = User::findFirstById($userId);
        if ($user) {

            $userAgent = $this->request->getUserAgent();
            $token = md5($user->email . $user->password . $userAgent);

            if ($cookieToken == $token) {

                $remember = RememberTokens::findFirst(array(
                    'usersId = ?0 AND token = ?1',
                    'bind' => array(
                        $user->id,
                        $token
                    )
                ));
                if ($remember) {

                    // Check if the cookie has not expired
                    if ((time() - (86400 * 8)) < $remember->createdAt) {

                        // Check if the user was flagged
                        $this->checkUserFlags($user);

                        // Register identity
                        $this->session->set('auth-identity', array(
                            'id' => $user->id,
                            'name' => $user->name,
                            'profile' => $user->profile->name
                        ));

                        // Register the successful login
                        $this->saveSuccessLogin($user);

                        return $this->response->redirect('users');
                    }
                }
            }
        }

        $this->cookies->get('RMU')->delete();
        $this->cookies->get('RMT')->delete();

        return $this->response->redirect('session/login');
    }

    /**
     * Checks if the user is banned/inactive/suspended
     *
     * @param TBS\Models\Users $user
     */
    public function checkUserFlags(\User $user)
    {
        if ($user->active != 'Y') {
            throw new Exception('The user is inactive');
        }

        if ($user->banned != 'N') {
            throw new Exception('The user is banned');
        }

        if ($user->suspended != 'N') {
            throw new Exception('The user is suspended');
        }
    }

    /**
     * Returns the current identity
     *
     * @return array
     */
    public function getIdentity()
    {
        return $this->session->get('auth-identity');
    }

    /**
     * Returns the current identity
     *
     * @return string
     */
    public function getName()
    {
        $identity = $this->session->get('auth-identity');
        return $identity['name'];
    }

    /**
     * Returns the current user's id
     *
     * @return string
     */
    public function getId()
    {
        $identity = $this->session->get('auth-identity');
        return $identity['id'];
    }

    /**
     * Removes the user identity information from session
     */
    public function remove()
    {
        if ($this->cookies->has('RMU')) {
            $this->cookies->get('RMU')->delete();
        }
        if ($this->cookies->has('RMT')) {
            $this->cookies->get('RMT')->delete();
        }

        $this->session->remove('auth-identity');
    }

    /**
     * Auths the user by his/her id
     *
     * @param int $id
     */
    public function authUserById($id)
    {
        $user = \User::findFirstById($id);
        if ($user == false) {
            throw new Exception('The user does not exist');
        }

        $this->checkUserFlags($user);

        $this->session->set('auth-identity', array(
            'id' => $user->id,
            'name' => $user->name
            // 'profile' => $user->profile->name
        ));
    }

    /**
     * Get the entity related to user in the active identity
     *
     * @return \TBS\Models\Users
     */
    public function getUser()
    {
        $identity = $this->session->get('auth-identity');
        if (isset($identity['id'])) {

            $user = \User::findFirstById($identity['id']);
            if ($user == false) {
                throw new Exception('The user does not exist');
            }

            return $user;
        }

        return false;
    }
}
