<?php

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\PhalconFacebookRedirectLoginHelper as FacebookRedirectLoginHelper;
use Facebook\FacebookJavaScriptLoginHelper;

class FacebookController extends ControllerBase
{

	private $_session;
	
	public function initialize() 
	{
		FacebookSession::setDefaultApplication($this->config->facebook->appId, $this->config->facebook->appSecret);		
	}
	
    public function indexAction()
    {
	}
    
    /*
     * This should be called after a succesful login to Facebook on mobile  
     * @param email 
     * @param token 
     * @param token_expires
     * @param client_session_key
     * 
     * TODO: the app has to ask for user_location as well
    */
	public function loginAction()  {

		try {
			 $logger = $this->getDI()->get("logger");
			 
			 $this->setJsonResponse();

			 $token 		= $this->request->getPost("token");
			 $token_expires = $this->request->getPost("token_expires");
			 $email 		= $this->request->getPost("email");
			 $client_session_key = $this->request->getPost("client_session_key");
			 $facebook_id   = $this->request->getPost("facebook_id");
			 			 
			 if (!$token)  {
				return array("success" => 0, "message" =>  "No token provided !");
			 }

			$logger->log("Create Facebook session...");
			$this->_session = new FacebookSession($token);
			
			if (!$this->_session->Validate())
				return array("success" => 0, "message" => "Not a valid token!");

			// is this the first time he connects with Fb ? 
			$user = User::findFirstByEmail($email);
			
			if (!$user) {
						$logger->log("Fetching your info...");
						
						$request = new FacebookRequest($this->_session, 'GET', '/me');
						$response = $request->execute();
						// get response
						$user_info = $response->getGraphObject()->asArray();

						// Create new User
						$user = new User();
						$user->assign(array("email" =>  $email,
											"password" => $this->security->hash(generateHash(7)), // generated password
											"name" => $user_info["first_name"] . " " . $user_info["last_name"],
											"birthdate"=> isset($user_info["birthday"]) ? $user_info["birthday"] : NULL,
											"registration_date" => date("d-m-Y"),
											"gender" => $user_info["gender"],
											"country" => "RO", // TBD
											"city" => @$user_info["location"]->name,
											"active" => "Y",
											"is_facebook_connect" => 1,
											"facebook_id" => $user_info["id"]));
						
						if (!$user->create()) {
							foreach ($user->getMessages() as $message)
								$messages .= $message . PHP_EOL;
							
							return array("success" => 0, "message" => $messages);
						} else {
							$logger->log("Created new user ");
						}
						
						// save profile photo to local folder
/*
						if ($user_info["id"]) {
							$photo_url = "https://graph.facebook.com/" . $user_info["id"] . "/picture?type=$photo_size";
							
							$photo = file_get_contents($photo_url);
							$filename = $this->config->photoDir . "/" . $user->id 
							file_put_contents($filename, $photo);
						}
*/						
			} elseif (is_null($user->facebook_id) && !is_null($facebook_id)) {
				$user->facebook_id = $facebook_id;
				$user->save();
			}
			
			// get Facebook friends 
/*
			$logger->log("get Facebook friends ...");
			
			$friends = $this->getFriendsList();
			$user->importFriendsList($friends);
*/						
			// insert/update musical activity 
			$musical_stream = $this->getMusicActivity();
			$user->importFacebookMusicActivity($musical_stream);

			// check playlist 
			$my_playlist = UserPlaylist::findFirst("user_id = ". $user->id . " AND status = 'open'");
			
			if ($my_playlist) {				
				//if (date("Y-m-d") != $my_playlist->create_date) {
					$my_playlist->save(array("status" => 'closed'));
				//}	
			} 
			
			// create new playlist
			UserPlaylist::registerNewPlaylist($user->id, $email);
			
			// register new session 
			$session_key = $this->auth->registerSession($user, $client_session_key, $token, $token_expires);
			
			// playlists 
			$playlists = UserPlaylist::getUserPlaylists($user->id);
			
			return array("success" => 1, "user_id" => $user->id, "session_key" => $session_key, "playlists" => $playlists);
		
		} catch(Exception $ex) {
				return array("success" => 0, "message" => $ex->getMessage());
		}

	}

	// Get Facebook musical activity     
	public function getMusicActivity() {
		$music_activity = array();
		$logger = $this->getDI()->get("logger");
		
		try {
			$request = new FacebookRequest( $this->_session, 'GET', '/me/music' );
			$response = $request->execute();
			$music_activity = $response->getGraphObject()->asArray();	
			
		} catch (FacebookRequestException $ex) {
			$logger->error($ex->getMessage());
		}
		
		return $music_activity;
	}
	
	public function getPersonalInfoAction() {
		
	}
	
	public function getFriendsList() {
			$request = new FacebookRequest($this->_session, 'GET', '/me/friends');
			$response = $request->execute();
			
			$friends_list = $response->getGraphObject()->asArray();
		
			return $friends_list;
	}
	
	
}

