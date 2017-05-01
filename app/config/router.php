<?php
$router = new \Phalcon\Mvc\Router();


$router->add("/deezer", array(
	'controller' => 'deezer',
	'action' => 'index'
));

$router->add('/confirm/{code}/{email}', array(
    'controller' => 'user_control',
    'action' => 'confirmEmail'
));

$router->add('/reset-password/{code}/{email}', array(
    'controller' => 'user_control',
    'action' => 'resetPassword'
));

// routes for WEB SERVICE 
$router->add('/ws/signup', array(
	'controller' => 'webservice',
	'action' => 'signup'
));

$router->add('/ws/login', array(
	'controller' => 'webservice',
	'action' => 'login'
));

$router->add('/ws/vote', array(
		'controller' => 'vote',
		'action' => 'votesong'
));



$router->add('/ws/v2/suggestions', array(
		'controller' => 'suggestion',
		'action' => 'GetSuggestions'
));

$router->add('/ws/v2/suggestions/{user_id}', array(
		'controller' => 'suggestion',
		'action' => 'GetSuggestions'
));

$router->add('/ws/v2/nextsuggestion', array(
		'controller' => 'suggestion',
		'action' => 'getNextSuggestion'
));

$router->add('/ws/v2/nextsuggestion/{user_id}', array(
		'controller' => 'suggestion',
		'action' => 'getNextSuggestion'
));

$router->add('/ws/suggestions', array(
		'controller' => 'webservice',
		'action' => 'suggestions'
));

$router->add('/ws/nextsuggestion', array(
		'controller' => 'webservice',
		'action' => 'nextsuggestion'
));

$router->add('/ws/gettopsongs', array(
		'controller' => 'webservice',
		'action' => 'gettopsongs'
));

$router->add('/ws/saveorupdatePlaylist', array(
		'controller' => 'webservice',
		'action' => 'saveorupdatePlaylist'
));

$router->add("/ws/fblogin", array(
    'controller' => 'facebook',
    'action' => 'login',
));

$router->add("/ws/checksession", array(
    'controller' => 'session',
    'action' => 'check',
));

$router->add("/ws/deezer-login", array(
    'controller' => 'deezer',
    'action' => 'login',
));

$router->add("/ws/get-album/{album_id}", array(
    'controller' => 'webservice',
    'action' => 'getAlbum',
));

$router->add("/ws/deezer-login", array(
    'controller' => 'deezer',
    'action' => 'login',
));

$router->add("/ws/getPlaylists", array(
    'controller' => 'webservice',
    'action' => 'getPlaylists',
));

$router->add("/ws/getSimilar", array(
    'controller' => 'suggestion',
    'action' => 'getSimilarArtists',
));

$router->add('/ws/getSuggestions', array(
		'controller' => 'suggestion',
		'action' => 'GetSuggestions'
));

$router->add('/ws/pwdrecovery', array(
	'controller' => 'webservice',
	'action' =>  'passwordRecovery'
));

$router->add('/ws/playsong', array(
	'controller' => 'webservice',
	'action' =>  'playSong'
));

$router->add('/ws/getuserprofile', array(
	'controller' => 'webservice',
	'action' =>  'getUserProfile'
));

$router->add('/ws/search', array(
	'controller' => 'webservice',
	'action' =>  'search'
));
$router->add('/ws/searchArtists', array(
	'controller' => 'webservice',
	'action' =>  'searchArtists'
));
$router->add('/ws/searchAlbums', array(
	'controller' => 'webservice',
	'action' =>  'searchAlbums'
));
$router->add('/ws/resetMusicFilter', array(
	'controller' => 'webservice',
	'action' =>  'resetMusicFilter'
));
$router->add('/ws/getMusicStyle', array(
	'controller' => 'webservice',
	'action' =>  'getMusicStyle'
));
$router->add('/ws/searchFiltres', array(
	'controller' => 'webservice',
	'action' =>  'searchFiltres'
));
$router->add('/ws/saveMusicFilter', array(
	'controller' => 'webservice',
	'action' =>  'saveMusicFilter'
));
return $router;
?>
