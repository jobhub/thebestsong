<?php

class DeezerController extends ControllerBase
{
	public function indexAction() {
		echo "Welcome to Deezer";
	}

	// this function should be called after succesfull login to Deezer
	// it assumes you already have a user_session entry 
	// it scans your artists and add them to User_Deezer_Artist 
	public function loginAction() {
		$this->setJsonResponse();
		$logger = $this->getDI()->get("logger");
		$max_artists = 100;
		
		// sanitize input
		$user_id 		= $this->request->getPost("user_id");
		$token 			= $this->request->getPost("token");
		$token_expires 	= $this->request->getPost("token_expires");
		
		if (!$user_session = $this->checkSession())
			return array("success" => 0, "message" => "Invalid session ");
			
		$user = User::findFirstById($user_id);
		if (!$user)
			return array("success" => 0, "message" => "User not found");
		
		try {
				// get Deezer Artists 
				$my_artists = DeezerApi::GetMyArtists($token);
				
				if (count($my_artists) < $max_artists)	{	
					// get Deezer playlist Artists 
					$playlist_artists = DeezerApi::getMyPlaylistArtists($token, $max_artists - count($my_artists));
					
					$deezer_artists = $my_artists + $playlist_artists;
				} else 
					$deezer_artists = $my_artists;
								
				if (!empty($deezer_artists)) {
					$user->importDeezerArtists($deezer_artists);
				} else {
					$logger->log("No data found for Deezer artists");
				}
			
		} catch (Exception $e) {
			$logger->log($e->getMessage());
		}
							
		$user_session->deezer_token = $token;
		$user_session->deezer_expires_at = $token_expires;
		
		if (!$user_session->save()) {
			return array("success" => 0, "message" => $user_session->getMessages()[0]);
		}
		
		return array("success" => 1);

		
	}
}

?>
