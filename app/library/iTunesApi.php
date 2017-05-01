<?php

namespace iTunes;

// return iTunes album-url or NULL if nothing found
function getAlbumURL($album_name, $artist_name) {
		if (is_null($album_name))
			return NULL;
			
		$iTunesRequest = "https://itunes.apple.com/search?term=" . urlencode($album_name . "+" . $artist_name) ."&entity=album";
		
		$content = file_get_contents($iTunesRequest, true);
		$result = json_decode($content);
		
		if (!empty($result->results)) 
			return $result->results[0]->collectionViewUrl;
		else 
			return NULL;
}	

// return iTunes track-view URL or NULL if nothing found
function getTrackURL($track_name, $artist_name) {
		$iTunesRequest = "https://itunes.apple.com/search?term=" . urlencode($track_name . "+" . $artist_name) ."&entity=song";
		
		$content = file_get_contents($iTunesRequest, true);
		$result = json_decode($content);
		
		if (!empty($result->results)) 
			return $result->results[0]->trackViewUrl;
		else 
			return NULL;
	
	
}

// return iTunes preview URL or NULL if nothing found
function getPreviewURL($track_name, $artist_name) {
		$iTunesRequest = "https://itunes.apple.com/search?term=" . urlencode($track_name . "+" . $artist_name) ."&entity=song";
		
		$content = file_get_contents($iTunesRequest, true);
		$result = json_decode($content);
		
		if (!empty($result->results)) 
			return $result->results[0]->previewUrl;
		else 
			return NULL;		
}
?>
