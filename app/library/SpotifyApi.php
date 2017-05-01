<?php

namespace Spotify;

function getAlbumURL($album_id) {
	if (!$album_id)
		return null;
		
	return "spotify:album:" . $album_id;
}

?>
