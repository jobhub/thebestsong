<?php

use Phalcon\Tag as Tag;

// use TBS\Models\User;

class WebserviceController extends ControllerBase {

    public function initialize() {
        //$this->setJsonResponse();	
    }

    public function indexAction() {
        
    }

    /**
     * This actions receive the input for login
     *
     * @param client_session_key
     * @param login
     * @param password
     * @param remember ?
     * 
     * @return array {"success":1, "session_key"(string), "user_id"}  OR 
     * 		   array {"success":0, "message" : error message}
     * 
     */
    public function loginAction() {
        try {
            if ($this->request->isPost()) {
                $login = $this->request->getPost('login');
                $session_key = $this->auth->check(array(
                    'login' => $login,
                    'password' => $this->request->getPost('password'),
                    'client_session_key' => $this->request->getPost('client_session_key'),
                    'remember' => $this->request->getPost('remember')));
				
				$user_id =  $this->auth->getId();

				// check playlist 
				$my_playlist = UserPlaylist::findFirst("user_id = ". $user_id . " AND status = 'open'");
				
				if ($my_playlist) {
					//if (date("Y-m-d") != $my_playlist->create_date) {
						$my_playlist->save(array("status" => 'closed'));
					//}	
				} 

				// create new playlist  
				UserPlaylist::registerNewPlaylist($user_id, $login);
				
				$playlists = UserPlaylist::getUserPlaylists($user_id);
				
                print json_encode(array("success" => 1, "user_id" => $this->auth->getId(), "session_key" => $session_key, "playlists" => $playlists));
            }
        } catch (Exception $e) {
            print json_encode(array("success" => 0, "message" => $e->getMessage()));
        }
    }

    /**
     * This actions receives sign up requests
     *
     */
    public function signupAction() {
        $this->view->disable();
        // get fields from POST 
        $request = $this->request;

        $name = $request->getPost('name', array('string', 'striptags'));
        //$name 	= $request->getPost('name');
        $email = $request->getPost('email');
        $password = $request->getPost('password');

        $newUser = new User();
        $result = $newUser->register($name, $email, $password);

        print json_encode($result);
    }

    public function getSuggestionArtists($hotttnesss, $user_id, $force_top_artists) {
        $user_artists = array();
        $sim_artists = array();

        $result_fb_artists = $this->modelsManager->executeQuery("
                                    SELECT DISTINCT UserFbMusicalActivity.artist_id 
                                    FROM UserFbMusicalActivity
                                    LEFT JOIN Song ON Song.artist_id = UserFbMusicalActivity.artist_id
                                    WHERE UserFbMusicalActivity.user_id = " . $user_id . "
                                    AND Song.id IS NOT NULL
                                    AND Song.deezer_preview_url IS NOT NULL");

        $fb_artists = $result_fb_artists->toArray();

        $result_dz_artists = $this->modelsManager->executeQuery("
                                    SELECT DISTINCT UserDeezerArtist.artist_id 
                                    FROM UserDeezerArtist
                                    LEFT JOIN Song ON Song.artist_id = UserDeezerArtist.artist_id
                                    WHERE UserDeezerArtist.user_id = " . $user_id . "
                                    AND Song.id IS NOT NULL
                                    AND Song.deezer_preview_url IS NOT NULL ");

        $dz_artists = $result_dz_artists->toArray();

        $result_top_artists = $this->modelsManager->executeQuery("
                                    SELECT DISTINCT Artist.id AS artist_id
                                    FROM Artist
                                    LEFT JOIN Song ON Song.artist_id = Artist.id
                                    WHERE Artist.hotttnesss >= " . $hotttnesss . "
                                    AND Song.id IS NOT NULL
                                    AND Song.deezer_preview_url IS NOT NULL 
                                    ORDER BY Artist.hotttnesss DESC");

        $top_artists = $result_top_artists->toArray();

        $result_fbtop_artists = $this->modelsManager->executeQuery("
                                    SELECT DISTINCT Artist.id AS artist_id
                                    FROM Artist
                                    LEFT JOIN Song ON Song.artist_id = Artist.id
                                    WHERE Artist.hotttnesss >= " . $hotttnesss . "
                                    AND Song.id IS NOT NULL
                                    AND Song.deezer_preview_url IS NOT NULL 
                                    ORDER BY Artist.hotttnesss DESC");

        $fbtop_artists = $result_fbtop_artists->toArray();

        if ((!empty($fb_artists) || !empty($dz_artists)) && !$force_top_artists) {
            for ($i = 0; $i < count($fb_artists); $i++) {
                $sim_artists[] = $fb_artists[$i]['artist_id'];

                $result_sim_artists = $this->modelsManager->executeQuery("
                                    SELECT DISTINCT Song.artist_id
                                    FROM ArtistSimilarity 
                                    LEFT JOIN Song ON Song.artist_id IN (ArtistSimilarity.similar_artists_ids)
                                    WHERE ArtistSimilarity.artist_id = " . $fb_artists[$i]['artist_id'] . "
                                    AND Song.id IS NOT NULL
                                    AND Song.deezer_preview_url IS NOT NULL");

                $sim_artists_fb = $result_sim_artists->toArray();

                for ($j = 0; $j < count($sim_artists_fb); $j++) {
                    $sim_artists[] = $sim_artists_fb[$j]['artist_id'];
                }
            }

            for ($i = 0; $i < count($dz_artists); $i++) {
                $sim_artists[] = $dz_artists[$i]['artist_id'];

                $result_sim_artists = $this->modelsManager->executeQuery("
                                    SELECT DISTINCT Song.artist_id
                                    FROM ArtistSimilarity 
                                    LEFT JOIN Song ON Song.artist_id IN (ArtistSimilarity.similar_artists_ids)
                                    WHERE ArtistSimilarity.artist_id = " . $dz_artists[$i]['artist_id'] . "
                                    AND Song.id IS NOT NULL
                                    AND Song.deezer_preview_url IS NOT NULL");

                $sim_artists_dz = $result_sim_artists->toArray();

                for ($j = 0; $j < count($sim_artists_dz); $j++) {
                    $sim_artists[] = $sim_artists_dz[$j]['artist_id'];
                }
            }

            $user_artists = array_merge($user_artists, $fb_artists);
            $user_artists = array_merge($user_artists, $dz_artists);

            for ($i = 0; $i < count($fbtop_artists); $i++) {
                if (!empty($fbtop_artists[$i]['artist_id'])) {


                    if (in_array($fbtop_artists[$i]['artist_id'], $sim_artists)) {
                        $artist = array('artist_id' => $fbtop_artists[$i]['artist_id']);

                        if ($user_artists < 3) {
                            $song = $this->getSuggestionsForArtist($fbtop_artists[$i]['artist_id'], $user_id);

                            if (!empty($song)) {
                                $artist['song'] = $song;
                                $user_artists[] = $artist;
                            }
                        } else {
                            $user_artists[] = $artist;
                        }
                    }
                }
            }

            if (count($user_artists) < 10) {
                $user_artists = array_merge($user_artists, $top_artists);
            }
        } else {
            $user_artists = $top_artists;
        }

        return $user_artists;
    }

    public function getSuggestionsForArtist($artist_id, $user_id) {
        $result_songs = array();

        if (!empty($artist_id)) {
            $sql = "SELECT DISTINCT Song.id,
                            Song.song_name, 
                            Song.artist_id,
                            Song.artist_name,
                            Song.album_id,
                            Song.album_name,
                            Song.deezer_track_id,
                            Song.hotttnesss,
                            Song.votes_number,
                            Song.general_score,
                            Song.deezer_preview_url,
                            Album.album_visual
                    FROM Song
                    JOIN Album ON Album.id = Song.album_id
                    LEFT JOIN AlbumUserVote ON (Song.album_id = AlbumUserVote.album_id AND AlbumUserVote.user_id = " . $user_id . ")
                    LEFT JOIN UserDislikedSongs ON (Song.id = UserDislikedSongs.song_id AND UserDislikedSongs.user_id = " . $user_id . ")
                    WHERE Song.deezer_preview_url IS NOT NULL 
                    AND AlbumUserVote.id IS NULL
                    AND UserDislikedSongs.id IS NULL 
                    AND Song.artist_id = " . $artist_id;

            $result = $this->modelsManager->executeQuery($sql);

            $result_songs = $result->toArray();
        }

        return $result_songs[0];
    }

    public function getSuggestions($user_id, $amount, $force_top_artists) {
        $songs = array();
        $min_hotttnesss = 0.7;
        $user_artists = $this->getSuggestionArtists($min_hotttnesss, $user_id, $force_top_artists);

        while (count($user_artists) < 10 && $min_hotttnesss > 0) {
            $min_hotttnesss -= 0.05;
            $user_artists = $this->getSuggestionArtists($min_hotttnesss, $user_id, $force_top_artists);
        }

        if (!empty($user_artists)) {
            $rand_keys = array_rand($user_artists, 10);

            for ($i = 0; $i < count($rand_keys); $i++) {
                $song = array();
                if (!empty($user_artists[$rand_keys[$i]]['song'])) {
                    $song = $user_artists[$rand_keys[$i]]['song'];
                } else {
                    $song = $this->getSuggestionsForArtist($user_artists[$rand_keys[$i]]['artist_id'], $user_id);
                }

                if (!empty($song) && $i >= 1 && $song['id'] != $songs[count($songs) - 1]['id']) {
                    $songs[] = $song;
                }

                if (count($songs) == $amount)
                    break;
            }
        }

        if (empty($songs) || count($songs) != $amount) {
            $songs = $this->getSuggestions($user_id, $amount, true);
        }

        return $songs;
    }

    /**
     * This actions receive suggestions requests
     *
     */
    public function suggestionsAction() {
        $this->response->setContentType('application/json', 'UTF-8');

        $user_id = $this->request->getPost('user_id');

        if (empty($user_id))
            $user_id = 90;

        $songs = $this->getSuggestions($user_id, 3, false);

        for ($i = 0; $i < count($songs); $i++) {
            // get album songs

            $songs[$i]['album_songs'] = array();

            if (!empty($songs[$i]['album_id'])) {
                $sql = "SELECT id,
                                song_name,
                                deezer_preview_url,
                                deezer_track_id,
                                duration
                        FROM Song
                        WHERE deezer_preview_url IS NOT NULL
                        AND album_id = " . $songs[$i]['album_id'] . "
                        ORDER BY id";

                $result = $this->modelsManager->executeQuery($sql);
                $songs[$i]['album_songs'] = $result->toArray();
            }

            if (!is_numeric($songs[$i]['votes_number'])) {
                $songs[$i]['votes_number'] = 0;
            }

            if (!is_numeric($songs[$i]['general_score'])) {
                $songs[$i]['general_score'] = 0;
            }
        }

        print json_encode($songs);
    }

    /**
     * This actions receive suggestions requests
     *
     */
    public function nextsuggestionAction() {
        $user_id = $this->request->getPost('user_id');

        if (empty($user_id))
            $user_id = 90;

        $songs = $this->getSuggestions($user_id, 1, false);

        for ($i = 0; $i < count($songs); $i++) {
            // get album songs

            $songs[$i]['album_songs'] = array();

            if (!empty($songs[$i]['album_id'])) {
                $sql = "SELECT id,
                                song_name,
                                deezer_preview_url,
                                deezer_track_id,
                                duration
                        FROM Song
                        WHERE deezer_preview_url IS NOT NULL
                        AND album_id = " . $songs[$i]['album_id'] . "
                        ORDER BY id";

                $result = $this->modelsManager->executeQuery($sql);
                $songs[$i]['album_songs'] = $result->toArray();
            }

            if (!is_numeric($songs[$i]['votes_number'])) {
                $songs[$i]['votes_number'] = 0;
            }

            if (!is_numeric($songs[$i]['general_score'])) {
                $songs[$i]['general_score'] = 0;
            }
        }

        print json_encode($songs[0]);
    }

    /*
     * 
     * Function voteSongAction - vote for a song (up or down)
     *
     * @param client_session_key
     * @param session_key
     * @param user_id
     * @param song_id
     * @param vote (int) 1/-1
     * 
     * 
     * @return success (int) 0/1
     * 
     */

    public function voteSongAction() {
        
    }

    /*
     * Function getAlbum 
     *
     * @param album_id 
     *  
     * @return JSON album object
     * 
     */

    public function getAlbumAction($album_id) {
        $this->setJSONResponse();
        $songs = array();

        if (!$this->checkSession())
            return array("success" => 0);

        $album = Album::findFirstById($album_id);

        if (!$album)
            return array("success" => 0);
        else {
            foreach ($album->getSongs() as $song)
                $songs[] = $song->song_name;

            $album->songs = $songs;

            return array("success" => 1, "album" => $album, "songs" => $songs);
        }
    }


    public function getPlaylistsAction() {
        $user_id = $this->request->getPost('user_id');
        
        if (empty($user_id)){
            $user_id = 84;
        }
        
        $playlists = UserPlaylist::getUserPlaylists($user_id);
        
        
        print json_encode($playlists);
    }

    
    public function saveorupdatePlaylistAction($playlist_id, $playlist_name) {
        
        $playlist_id = $_POST['playlist_id'];
        $playlist_name = $_POST['playlist_name'];
        
        $sql = "UPDATE UserPlaylist
               SET playlist_name = '" . $playlist_name . "'
               WHERE id = " . $playlist_id;
        
        $result = $this->modelsManager->executeQuery($sql);

        print json_encode($result);
    }
    
    public function passwordRecoveryAction() {
        $this->setJSONResponse();
        $email = $this->request->getPost("email");

        // check email exists 
        $user = User::findFirstByEmail($email);

        if (!$user)
            return array("success" => 0, "message" => "Email not found");

        // create Reset Password 
        $rp = new ResetPasswords();
        $rp->usersId = $user->id;

        if (!$rp->create()) {
            foreach ($rp->getMessages() as $message)
                $error .= $message . PHP_EOL;

            return array("success" => 0, "message" => $error);
        }

        // send mail with reset password link 
        return array("success" => 1, "message" => "Sent mail with reset password link to $email");
    }

	/* NOT USED */
    public function playSongAction() {

        $this->setJSONResponse();
        return array("success" => 1);

        if (!$this->checkSession())
            return array("success" => 0, "message" => "Invalid session");

        $user_id = $this->request->getPost("user_id");
        $song_id = $this->request->getPost("song_id");

        $user = User::findFirstById($user_id);

        $current_playlist = UserPlaylist::getCurrentPlaylist($user_id, $user->email);

        if ($current_playlist->saveSong($song_id))
            return array("success" => 1);
        else
            return array("success" => 0, "message" => "Could not save song into playlist " . $current_playlist->playlist_name . PHP_EOL . $current_playlist->getMessages()[0]);
    }

    public function getUserProfileAction() {
        $result = array();
        $this->setJSONResponse();
        $photo_size = "normal";

        // if (!$this->checkSession())
        //	return array("success" => 0, "message" => "Invalid session");

        $user_id = $this->request->getPost("user_id");
        
        if (empty($user_id)){
            $user_id = 96;
        }

        $user = User::findFirstById($user_id);

        if (!$user)
            return array("success" => 0, "message" => "Unknown user");

        $result["name"] = $user->name;
        $result["photo"] = "https://graph.facebook.com/" . $user->facebook_id . "/picture?type=$photo_size";
        $result["best_songs"] = $user->getBestSongs();
        $result["playlists"] = UserPlaylist::find("user_id =  "  . $user_id)->toArray();

        return $result;
    }
    
    public function gettopsongsAction() {
        $sql = "SELECT AlbumBestSong.id,
                        AlbumBestSong.song_name,
                        AlbumBestSong.artist_id,
                        AlbumBestSong.artist_name,
                        AlbumBestSong.album_id,
                        AlbumBestSong.album_name,
                        AlbumBestSong.general_score,
                        AlbumBestSong.hotttnesss,
                        AlbumBestSong.deezer_track_id,
                        AlbumBestSong.deezer_preview_url,
                        AlbumBestSong.album_date,
                        AlbumBestSong.album_visual
                FROM AlbumBestSong
                ORDER BY AlbumBestSong.general_score DESC, AlbumBestSong.hotttnesss DESC LIMIT 20";

        $result = $this->modelsManager->executeQuery($sql);
        $topsongs = $result->toArray();

        print json_encode($topsongs);
    }
    
    public function searchAction() {
        $search_text = $this->request->getPost('search_text');
        
        $sql1 = "
                SELECT T4.id, T4.name, 
                (SELECT album_visual FROM Album WHERE artist_id = T4.id  ORDER BY general_score DESC LIMIT 1) AS album_visual 
                FROM(
                       SELECT * FROM(
                                SELECT * FROM(
                                        SELECT id, name,general_score,hotttnesss
                                        FROM Artist
                                        WHERE name LIKE '".$search_text."%'
                                        ORDER BY general_score, hotttnesss DESC
                                ) T1
                                UNION
                                SELECT * FROM(
                                        SELECT id, name,general_score,hotttnesss
                                        FROM Artist
                                        WHERE name LIKE '%".$search_text."%'
                                        ORDER BY general_score, hotttnesss DESC
                                ) T2
                        ) T3 LIMIT 1
                ) T4
                LIMIT 1
               ";

        $result = $this->db->query($sql1);
        $list_results['popular'] = $result->fetchAll();
        
        $sql2 = "
                 SELECT T4.id, T4.name,
                (SELECT album_visual FROM Album WHERE artist_id = T4.id  ORDER BY general_score DESC LIMIT 1) AS album_visual 
                 FROM(
                       SELECT * FROM(
                                SELECT * FROM(
                                        SELECT id, name,general_score,hotttnesss
                                        FROM Artist
                                        WHERE name LIKE '".$search_text."%'
                                        ORDER BY general_score, hotttnesss DESC
                                ) T1
                                UNION
                                SELECT * FROM(
                                        SELECT id, name,general_score,hotttnesss
                                        FROM Artist
                                        WHERE name LIKE '%".$search_text."%'
                                        ORDER BY general_score, hotttnesss DESC
                                ) T2
                        ) T3 LIMIT 3
                ) T4
                LIMIT 3
                ";

        $result = $this->db->query($sql2);
        $list_results['artists'] = $result->fetchAll();
        
        $sql3 = "
                SELECT * FROM (    
                        SELECT * FROM (
                                SELECT album_id AS id_album,
                                           album_name,
                                           artist_name,
                                           album_visual,
                                           general_score,
                                           hotttnesss
                                FROM Album_Best_Song
                                WHERE album_name like '" . $search_text . "%'
                                ORDER BY general_score, hotttnesss DESC
                        ) T1
                    UNION
                        SELECT * FROM (
                                SELECT album_id AS id_album,
                                           album_name,
                                           artist_name,
                                           album_visual,
                                           general_score,
                                           hotttnesss
                                FROM Album_Best_Song
                                WHERE album_name like '%" . $search_text . "%'
                                ORDER BY general_score, hotttnesss DESC
                        ) T2
                ) T
                LIMIT 3";

        $result = $this->db->query($sql3);
        $list_results['albums'] = $result->fetchAll();

        print json_encode($list_results);
    }

    public function searchArtistsAction() {
        $artist_id = $this->request->getPost('artist_id');
        $sql1 = "
                SELECT  T1.id, 
                        T1.name,
                       (
                         SELECT album_visual 
                             FROM Album 
                             WHERE artist_id = '".$artist_id ."'
                             ORDER BY general_score DESC 
                             LIMIT 1 
                        ) AS album_visual,
                        (
                             SELECT song_name
                             FROM artist_best_song
                             WHERE artist_id = '".$artist_id ."'
                        ) AS song_name
                FROM Artist T1
                WHERE T1.id = '".$artist_id ."'
               ";

        $result = $this->db->query($sql1);
        $response['artist_details'] = $result->fetchAll();
        
         $sql2 = "
                SELECT  T1.id,
                        T1.album_name, 
                        T1.album_visual,
                        T2.song_name
                FROM Album T1
                LEFT JOIN Album_Best_Song T2 ON T2.album_id = T1.id AND T2.artist_id = '".$artist_id ."'
                WHERE T1.artist_id = '".$artist_id ."'
                ORDER BY T1.album_date DESC
               ";

        $result = $this->db->query($sql2);
        $response['artist_albums'] = $result->fetchAll();

        print json_encode($response);
    }
    
     public function searchAlbumsAction() {
        $album_id = $this->request->getPost('album_id');
        $sql1 = "
                SELECT T1.id,
                       T1.album_name, 
                       T1.artist_name, 
                       T1.album_visual,
                       (
                         SELECT song_name 
                         FROM Album_Best_Song 
                         WHERE album_id = '".$album_id ."'
                        ) AS album_best_song
                FROM Album T1
                WHERE T1.id = '".$album_id ."'
               ";

        $result = $this->db->query($sql1);
        $response['album_details'] = $result->fetchAll();
        
         $sql2 = "
                    SELECT id, song_name
                    FROM Song 
                    WHERE album_id = '".$album_id ."'
                    ORDER BY hotttnesss DESC
               ";

        $result = $this->db->query($sql2);
        $response['album_songs'] = $result->fetchAll();

        print json_encode($response);
    }
    
    public function resetMusicFilterAction() {
        $user_id = $this->request->getPost('user_id');
        $sql1 = "
                UPDATE User_Filters_Search
                SET status = 0
                WHERE user_id = '".$user_id."'
               ";

        $result = $this->db->query($sql1);     

        print json_encode('Success');
    }
    
    public function getMusicStyleAction() {
        $sql1 = "
                SELECT T.style1_id, T.style1_name, T.album_visual, COUNT(*) AS nr
                FROM  Album_Best_Song T
                GROUP BY T.style1_name
                ORDER BY nr DESC
                LIMIT 20
               ";

        $result   = $this->db->query($sql1);  
        $response = $result->fetchAll();

        print json_encode($response);
    }
    
    public function searchFiltresAction() {
        
        $search_text = $this->request->getPost('search_text');
        $sql1 = "
                SELECT T4.id, T4.name,
                (SELECT album_visual FROM Album WHERE artist_id = T4.id  ORDER BY general_score DESC LIMIT 1) AS album_visual 
                FROM(
                       SELECT * FROM(
                                SELECT * FROM(
                                        SELECT id, name,general_score,hotttnesss
                                        FROM Artist
                                        WHERE name LIKE '".$search_text."%'
                                        ORDER BY general_score, hotttnesss DESC
                                ) T1
                                UNION
                                SELECT * FROM(
                                        SELECT id, name,general_score,hotttnesss
                                        FROM Artist
                                        WHERE name LIKE '%".$search_text."%'
                                        ORDER BY general_score, hotttnesss DESC
                                ) T2
                        ) T3
                ) T4
                LIMIT 30
                ";

        $result = $this->db->query($sql1);
        $list_results['artists'] = $result->fetchAll();
        
         $sql2 = "
                       SELECT * FROM(
                                SELECT * FROM(
                                        SELECT id, song_name, album_visual
                                        FROM Album_Best_Song
                                        WHERE song_name LIKE '".$search_text."%'
                                        ORDER BY general_score, hotttnesss DESC
                                ) T1
                                UNION
                                SELECT * FROM(
                                        SELECT id, song_name, album_visual
                                        FROM Album_Best_Song
                                        WHERE song_name LIKE '%".$search_text."%'
                                        ORDER BY general_score, hotttnesss DESC
                                ) T2
                       ) T3
                       LIMIT 30
                ";

        $result = $this->db->query($sql2);
        $list_results['songs'] = $result->fetchAll();
        
        print json_encode($list_results);
    }
    
    public function saveMusicFilterAction() {
        $user_id = $this->request->getPost('user_id');
        $filter_id = $this->request->getPost('filter_id');
        $filter_type = $this->request->getPost('filter_type');
        $sql1 = "
                SELECT COUNT(*) AS no_user_filters
                FROM User_Filters_Search
                WHERE user_id = '".$user_id."'
               ";

        $result = $this->db->query($sql1);     
        $response = $result->fetch();
        
       if($response['no_user_filters']==0){
             $sql2 = "
                INSERT INTO User_Filters_Search(user_id, value, type, status)
                VALUES('".$user_id."','".$filter_id."', '".$filter_type."',1)
               ";
             
             $this->db->query($sql2);  
        }
        else{
            $sql2 = "
                UPDATE User_Filters_Search
                SET  
                    value = '".$filter_id."', 
                    type = '".$filter_type."', 
                    status = 1
                WHERE user_id = '".$user_id."'
               ";
             
             $this->db->query($sql2);  
        }
        print $response['no_user_filters'];
    }
}
