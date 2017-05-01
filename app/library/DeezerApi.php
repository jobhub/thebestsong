<?php

class DeezerApi {
	// return an album object or NULL (if error or NOT FOUND)
	public static function getAlbumInfo($album_id) {
		
		$deezer_api_request = 'http://api.deezer.com/album/'.$album_id;
		$content = file_get_contents($deezer_api_request, true);
		$result = json_decode($content);		

		if (isset($result->error))
			return null;
		else 	
			return $result;
	}

	public static function getTrackInfo($track_id, $field = null) {
		$deezer_api_request = 'http://api.deezer.com/track/' . $track_id;
		
		$content = file_get_contents($deezer_api_request, true);
		$result = json_decode($content);		

		if (isset($result->error))
			return null;
		elseif (!is_null($field) && isset($result->$field))
			return $result->$field;
		else		
			return $result;
		
	}
	
	// Return a list with User's favourite artists
	public static function getMyArtists($token) {
		$results = array();
		
		if (!$token) 
			return null;
			
		$deezer_api_request = "https://api.deezer.com/user/me/artists?output=json&access_token=" . $token . "&version=js-v1.0.0";	
		
		$content = file_get_contents($deezer_api_request, true);
		$result = json_decode($content);		
		
		if (isset($result->error))
			return null;
		else {		
			foreach ($result->data as $item) {
				$results[$item->id] = $item->name;
			}			
		}
		
		return $results;		
	}
	
	public static function getUserPlaylists($token) {
		if (!$token) 
			return null;
			
		$deezer_api_request = "https://api.deezer.com/user/me/playlists?output=json&access_token=" . $token . "&version=js-v1.0.0";	
		
		$content = file_get_contents($deezer_api_request, true);
		$result = json_decode($content);		
		
		if (isset($result->error))
			return null;
		else		
			return $result->data;
				
	}
		
	// return artists from a given playlist	
	public static function getPlaylistArtists($playlist_id, $token)  
	{
		$artists = array();
		
		if (!$token) 
			return null;
		
		$deezer_api_request = "http://api.deezer.com/playlist/" . $playlist_id . "/tracks";

		$content 	= file_get_contents($deezer_api_request, true);
		$result 	= json_decode($content);

		if (isset($result->error))
			return null;
// print_r($result->data);			
		foreach ($result->data as $item) {
			$artists[$item->artist->id] = $item->artist->name;
		}	
		
		return $artists;
	}
	
	public static function getMyPlaylistArtists($token, $limit = 0) 
	{
		$my_artists = array();
		
		if (!$token) 
			return null;
			
		$playlists = self::getUserPlaylists($token);		
		
		if (!empty($playlists)) 
			foreach ($playlists as $pl) {
				$artists = self::getPlaylistArtists($pl->id, $token);
				
				if (!empty($artists)) 
					$my_artists = $my_artists + $artists;

				if ($limit && count($my_artists) > $limit)
					break;					
			}
		
		return $my_artists;
	}
}

?>
