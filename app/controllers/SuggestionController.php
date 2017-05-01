<?php

class SuggestionController extends ControllerBase {

    protected function initialize() {

        $this->limit_songs = 30;
        $this->min_hotttnesss = 0;
        $this->min_user_votes = 20;
        $this->nb_songs_returned = 3;
        // if less than this value new songs in USS, re-build suggestions
		$this->min_remaining_songs = 6;
		
        $this->weights = array(
			"getSimilarArtistsBestSongs" => 0.15,
			"getSimilarSongVoted" => 0.20,
			"getBestSongsSimilar10" => 0.10,
			"getAffiliatedVotedSongs" => 0.20,
			"getBestSongsOfBestlastStylesVoted" => 0.20,
			"GetBestSongOfMyFavorite" => 0.40,
			"getPopularSongs" => 0.001
        );
        $this->start_year = 2013;
        
        $this->logger = $this->getDI()->getShared("logger");
    }

    public function GetSuggestionsAction() {
		$suggested_artists = array();
		$results = array();
		$songs   = array();
		
        $this->setJsonResponse();
             
        $user_id  	= $this->request->getPost("user_id") ? $this->request->getPost("user_id") : $this->dispatcher->getParam("user_id");

        $session_key = $this->request->getPost("session_key");
        $limit_songs = $this->limit_songs;

        try {
            //if (!$this->checkSession())
				//return array("success" => 0);

            $songs = $this->buildSuggestions($user_id, $session_key, $limit_songs);
			
			while (empty($songs)) {
				$this->start_year--;
				$songs = $this->buildSuggestions($user_id, $session_key, $limit_songs);
			}

			// get last 4 suggested artists 
			$sql = "SELECT GROUP_CONCAT(a.artist_id SEPARATOR ',') AS artist_ids
					FROM (SELECT DISTINCT artist_id FROM Album_Best_Song S, User_Suggested_Songs U
					WHERE U.user_id = $user_id 
					AND U.status = 'sent' 
					AND U.song_id = S.id 
					ORDER BY U.sent_time DESC
					LIMIT 4) AS a";
					
			$res = $this->db->fetchOne($sql, Phalcon\Db::FETCH_OBJ);

			if ($res) {
				$artist_ids = $res->artist_ids;
				$suggested_artists = explode(',' , $artist_ids);
			}
			
            // return first 3 songs  and mark them as sent : make sure we don't have the same artist 
            foreach ($songs as $song) {
				if (!in_array($song->artist_id, $suggested_artists)) {
					$results[] = $song;
					$suggested_artists[] = $song->artist_id;
					
					// enough results ? 
					if (count($results) == $this->nb_songs_returned)
						break;
				}
			}
            // $results = array_slice($songs, 0, 3);
            $this->sendSuggestedSongs($user_id, $results);

            foreach ($results as &$item) {

                if (is_null($item->votes_number))
                    $item->votes_number = 0;

                if (is_null($item->general_score))
                    $item->general_score = 0;

                $item->album_songs = Song::getAlbumSongs($item->album_id);
            }
        } catch (Exception $ex) {
			$this->logger->error($ex->getMessage());
            return array("success" => 0, "message" => $ex->getMessage());
        }

        return $results;
    }
	
	private function setOrigin(&$songs, $origin) {
			foreach ($songs as &$song) {
				$song->origin = $origin;
			}
			
			return $songs;
	}
	
	
    private function buildSuggestions($user_id, $session_key, $limit_songs) {
        $suggesting_songs = array();
		$exclude_ids = null;
		$nb_returned_songs = $limit_songs;
		
		$user_votes = SongUserVote::findByUserId($user_id);
		
		if ($user_votes->count() < $this->min_user_votes) {
			$this->weights["GetBestSongOfMyFavorite"] = 0.80;
			$this->weights["getPopularSongs"] = 0.001; 
			$this->weights["getBestSongsSimilar10"] = 0.15;
			$this->weights["getSimilarArtistsBestSongs"]  = 0.20 ;
		}
		
		// check User_Filters_Search 
		$sql = "SELECT * FROM User_Filters_Search WHERE user_id = $user_id AND status = 1";
		$res = $this->db->query($sql);
		
		if ($res->numRows()) {
			// use filter-based suggestions 			
			$obj = $res->fetch();
			
			switch ($obj["type"]) {
				case "song" :
					$suggesting_songs = $this->GetSongsBySongSearch($user_id, $obj["value"]);
					break;
				case "artist":
					$suggesting_songs = $this->GetSongsByArtistSearch($user_id, $obj["value"]);
					break;
				case "style" :
					$suggesting_songs = $this->GetSongsByStylesSearch($user_id, $obj["value"]);
					break;
			}
			
			$this->setOrigin($suggesting_songs, "FLT");

			if (count($suggesting_songs) < $limit_songs) {
				$limit_songs -= count($suggesting_songs);
			
				// set filter to inactive
				$sql1 = "UPDATE User_Filters_Search
						 SET status = 0
						 WHERE user_id = $user_id ";

				$result = $this->db->query($sql1);  				
			} else 
				$limit_songs = 0;
			 
		}
			
		if ($limit_songs > 0) {
				
				// use Facebook and Deezer 
				$nb_songs = ceil($this->weights["GetBestSongOfMyFavorite"] * $limit_songs);
				$best_songs_of_favorites = $this->GetBestSongOfMyFavorite($user_id, $nb_songs);
				$this->setOrigin($best_songs_of_favorites, 'FAV');
			  
				$suggesting_songs +=  $best_songs_of_favorites;
				
				// Similar artists
				$nb_songs = ceil($this->weights["getSimilarArtistsBestSongs"] * $limit_songs);
				
				if (count($suggesting_songs))
					$exclude_ids = implode(',', array_keys($suggesting_songs));
				
				$similar_artists_bestsongs = $this->getSimilarArtistsBestSongs($user_id, $nb_songs, $exclude_ids);
				$this->setOrigin($similar_artists_bestsongs, 'SIM');
				$suggesting_songs = $suggesting_songs + $similar_artists_bestsongs;
				
				// array_splice($suggesting_songs, count($suggesting_songs), 0, $similar_artists_bestsongs);        
				
				// Best songs of similar top 10
				$nb_songs = ceil($this->weights["getBestSongsSimilar10"] * $limit_songs);
				$Best_songs_of_similar_top_10 = $this->getBestSongsSimilar10($user_id, $nb_songs);
				$this->setOrigin($Best_songs_of_similar_top_10, 'SIM10');

				$suggesting_songs += $Best_songs_of_similar_top_10;
				// array_splice($suggesting_songs, count($suggesting_songs), 0, $Best_songs_of_similar_top_10);

				// call these functions only if the user votes bypass $min_user_votes
				if ($user_votes->count() > $this->min_user_votes) {
					// The 20 best songs of artists similar as the user last votes
					$nb_songs = ceil($this->weights["getSimilarSongVoted"] * $limit_songs);
					$similar_song_voted = $this->getSimilarSongVoted($user_id, $nb_songs);
					$this->setOrigin($similar_song_voted, 'SIM_VOT');

					$suggesting_songs += $similar_song_voted;
					// array_splice($suggesting_songs, count($suggesting_songs), 0, $similar_song_voted);

					// Affiliated vote
					$nb_songs = ceil($this->weights["getAffiliatedVotedSongs"] * $limit_songs);
					$affiliated_voted_songs = $this->getAffiliatedVotedSongs($user_id, $nb_songs);
					$this->setOrigin($affiliated_voted_songs, 'AFI');

					$suggesting_songs += $affiliated_voted_songs;
					//array_splice($suggesting_songs, count($suggesting_songs), 0, $affiliated_voted_songs);

					// Best songs of Best last styles
					$nb_songs = ceil($this->weights["getBestSongsOfBestlastStylesVoted"] * $limit_songs);
					$best_styles_voted = $this->getBestSongsOfBestlastStylesVoted($user_id, $nb_songs);
					$this->setOrigin($best_styles_voted, 'BSV');

					$suggesting_songs += $best_styles_voted;
					// array_splice($suggesting_songs, count($suggesting_songs), 0, $best_styles_voted);		
				}
			
				// Popular songs
				if (count($suggesting_songs) < $nb_returned_songs) {
					
					$nb_songs = $nb_returned_songs - count($suggesting_songs); 			
					$popular_songs = $this->getPopularSongs($user_id, $nb_songs);
					$this->setOrigin($popular_songs, 'POP');

					$suggesting_songs += $popular_songs;
					//array_splice($suggesting_songs, count($suggesting_songs), 0, $popular_songs);
				}
		}	

        // sort the array by general_score and hotttnesss
/*        
          usort($suggesting_songs, function($a, $b) {
			  $a->total_score = (float)$a->general_score + (float)$a->hotttnesss;
			  $b->total_score = (float)$b->general_score + (float)$b->hotttnesss;

			  if ($a->total_score == $b->total_score)
			  return 0;

			  return ($a->total_score  < $b->total_score) ? 1 : -1;
          });
*/        
        // shuffle($suggesting_songs);
        $results = array_slice($suggesting_songs, 0, $nb_returned_songs);


        // keep the results into User_Suggested_Songs
        if (!$this->saveSuggestedSongs($results, $user_id, $session_key))
            $logger->error("Could not save suggestions to User_Suggested_Songs");

        return $results;
    }

    public function saveSuggestedSongs($results, $user_id, $session_key) {
        $sql = "INSERT IGNORE INTO `User_Suggested_Songs`(`user_id`, `song_id`, `session_key`, `origin`) VALUES ";

        foreach ($results as $row) {
            $sql .= '(' . $user_id . ',' . $row->id . ',"' . $session_key . '", "' . $row->origin . '"),';
        }

        return $this->db->execute(rtrim($sql, ','));
    }

    public function sendSuggestedSongs($user_id, $songs) {
        $song_ids = "";

        if (is_array($songs)) {
            foreach ($songs as $song) {
                $song_ids .= $song->id . ',';
            }
            
            $song_ids = rtrim(rtrim($song_ids), ',');
        } else 
			$song_ids = $songs->id;
				
        $sql = "UPDATE User_Suggested_Songs 
                SET status = 'sent', sent_time = NOW()
                WHERE user_id = $user_id AND song_id IN (" . $song_ids . ")";
            
        $res = $this->db->execute($sql);
		
		return $res;
    }

    /*
     * Get bestsongs for artists similar to my Facebook and/or Deezer artists 
     * TODO : filter with user_liked_songs and user_disliked_songs
     * @return array songs
     */

    public function getSimilarArtistsBestSongs($user_id, $limit = 3, $exclude_ids = null) {

        $results = array();
        $logger = $this->getDI()->get("logger");
        $str_similar_ids = '';

        // Get ids of similar artists of Facebook artists 
        $facebook_sql = "SELECT GROUP_CONCAT(`similar_artists_ids` SEPARATOR ',') AS str_ids
                                        FROM `Artist_Similarity` S 
                                        INNER JOIN User_Fb_Musical_Activity F ON S.artist_id = F.artist_id 
                                        WHERE F.user_id = $user_id 
                                        UNION 
						SELECT GROUP_CONCAT(`similar_artists_ids` SEPARATOR ',') AS str_ids
                                        FROM `Artist_Similarity` S 
                                        INNER JOIN User_Deezer_Artist D ON D.artist_id = S.artist_id 
                                        AND D.user_id = $user_id ";

        $rows = $this->db->fetchAll($facebook_sql, Phalcon\Db::FETCH_OBJ);

        if (!count($rows))
            return $results;

        foreach ($rows as $row) {
            $str_similar_ids .= $row->str_ids ? $row->str_ids . ',' : '';
        }

        $str_similar_ids = rtrim($str_similar_ids, ',');
        $str_similar_ids = str_replace(',,', ',', $str_similar_ids);

        if ($str_similar_ids == '')
            return $results;

        $songs = $this->getAlbumBestSong($user_id, $ids = $str_similar_ids, $limit, $exclude_ids);

        // $logger->log("Similar to : " . $artist->artist_name . PHP_EOL . print_r($songs, true));			

        shuffle($songs);
        $results = $songs;

        return $results;
    }

    // Return a list with artist's best songs (if $id != null) or 
    // 		  a list with artists' best songs (if $ids != null)

    public function getArtistBestSong($id = null, $ids = null, $limit = null) {
        $sql = "SELECT S.*
				FROM artist_best_song S 
				LEFT JOIN User_Liked_Songs U ON (U.song_id = S.id)
				LEFT JOIN User_Disliked_Songs D ON (D.song_id  = S.id)";

        if (!is_null($ids))
            $ids = rtrim(rtrim($ids), ',');
        
        if (!is_null($id))
            $where = " WHERE artist_id = $id ";
        elseif (!is_null($ids))
            $where = " WHERE artist_id IN (".$ids.") ";
        else
            return false;

        $sql .= $where . " AND U.id IS NULL AND D.id IS NULL 
		ORDER BY S.general_score DESC, S.hotttnesss DESC ";

        $sql .= (!is_null($limit) ? " LIMIT $limit " : "");

        $results = $this->db->fetchAll($sql, Phalcon\Db::FETCH_OBJ);

        return $results;
    }

/*
 * function getAlbumBestSong 
 * 
 * @param int $user_id  
 * @param string $artist_ids 
 * @param int $limit
 * @param string $exclude_song_ids : comma-separated list of song ids to exclude 
 * 
 * @return array 
*/
    public function getAlbumBestSong($user_id, $artist_ids, $limit = 30, $exclude_song_ids = null) 
    {
        $results = array();
        
        if ($artist_ids != '') {                       
            $artist_ids_arr = array();
            $arr = explode(",", $artist_ids);
            
            for ($i = 0; $i<count($arr); $i++) {
                
                if (is_numeric($arr[$i]) && !in_array($arr[$i], $artist_ids_arr)) {
                    $artist_ids_arr[] = $arr[$i];
                }
            }
            
            $artist_ids = implode(",", $artist_ids_arr);
            
            $sql = "SELECT A.id, 
					song_name,
					artist_id, 
					artist_name,
					album_id, 
					album_name,
					deezer_track_id, 
					hotttnesss,
					IFNULL(votes_number, 0) as votes_number,
					IFNULL(general_score, 0) as general_score,
					deezer_preview_url,
					album_visual,
					song_duration		
					FROM Album_Best_Song A 
					LEFT JOIN User_Suggested_Songs S ON S.song_id = A.id AND S.user_id = $user_id 
					WHERE artist_id IN (".$artist_ids.")
					AND S.song_id IS NULL  
					AND album_year >= " . $this->start_year  . 
					($this->min_hotttnesss ? " AND hotttnesss > " . $this->min_hotttnesss  : "") . 					
					(!is_null($exclude_song_ids) ? " AND A.id NOT IN ($exclude_song_ids) " : "") . 
					" ORDER BY album_date DESC, general_score DESC, hotttnesss DESC
					LIMIT $limit ";

            $result = $this->db->query($sql);
            $result->setFetchMode(Phalcon\Db::FETCH_OBJ);
            
            while ($row = $result->fetch()) 
				$results[$row->id] = $row;
            
        } 
        
		return $results;
    }

    public function getNextSuggestionAction() {
        $this->setJSONResponse();
        $song = null;
		/*
          if (!$this->checkSession())
          return array("success" => 0);
         */
         
        $user_id = $this->request->getPost("user_id") ? $this->request->getPost("user_id") : $this->dispatcher->getParam("user_id");
        
        $session_key = $this->request->getPost("session_key");
		
		// get last 4 suggested artists 
		$sql = "SELECT GROUP_CONCAT(a.artist_id SEPARATOR ',') AS artist_ids
				FROM (SELECT DISTINCT artist_id FROM Album_Best_Song S, User_Suggested_Songs U
				WHERE U.user_id = $user_id 
				AND U.status = 'sent' 
				AND U.song_id = S.id 
				ORDER BY U.sent_time DESC
				LIMIT 4) AS a";
				
		$res = $this->db->fetchOne($sql, Phalcon\Db::FETCH_OBJ);
		if ($res)
			$artist_ids = $res->artist_ids;
		
        try {
            // get one song from User_Suggested_Songs
            $sql = "SELECT  S.id,
                        S.song_name, 
                        S.artist_id,
                        S.artist_name,
                        S.album_id,
                        S.album_name,
                        S.deezer_track_id,
                        S.hotttnesss,
                        IFNULL(S.votes_number, 0) as votes_number,
                        IFNULL(S.general_score, 0) as general_score,
                        S.deezer_preview_url,
						S.album_visual,
						S.song_duration
						FROM Album_Best_Song S, User_Suggested_Songs  U
						WHERE U.user_id = $user_id AND U.status = 'new' 
						AND U.song_id = S.id ".
						(isset($artist_ids) ? " AND S.artist_id NOT IN ($artist_ids) " : "") . 						
						" ORDER BY U.id DESC ";

            $songs = $this->db->fetchAll($sql, Phalcon\Db::FETCH_OBJ);

            // if we run out of suggestions
            if (empty($songs) || count($songs) < $this->min_remaining_songs) {
                // call suggestions
                $songs = $this->buildSuggestions($user_id, $session_key, $this->limit_songs);
				// check not repeating artists 
				if (isset($artist_ids)) {
					$suggested_artists = explode(',', $artist_ids);
					
					foreach ($songs as $item)
						if (!in_array($item->artist_id, $suggested_artists)) {
							$song = $item;
							break;
						}
				}
            } else {
				$song = $songs[0];
			}			
            
            // mark it as sent 
            $this->sendSuggestedSongs($user_id, $song);

            $song->album_songs = Song::getAlbumSongs($song->album_id);

            return $song;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return array("success" => 0, "message" => $e->getMessage());
        }

        return array("success" => 1, "songs" => $song);
    }


    /**
     * Get best songs for similar artists of the user's votes
     */
    public function getSimilarSongVoted($user_id, $limit = 3) {

        $similar_song_voted = array();
        $similar_artists_ids = array();
			
        // GET a list with similar_artists_ids as the user's votes
        $q1 = "SELECT GROUP_CONCAT(DISTINCT S.similar_artists_ids SEPARATOR ',') AS similar_artists_ids
				   FROM Artist_Similarity S, Artist_User_Vote V
				   WHERE V.user_id = $user_id 
				   AND S.artist_id = V.artist_id 
				   ORDER BY V.vote_date DESC ";
        // LIMIT 20";

        $result = $this->db->fetchOne($q1, Phalcon\Db::FETCH_OBJ);
        $similar_artists_ids = explode(",", $result->similar_artists_ids);

        // choose randomly 20 artists 
        shuffle($similar_artists_ids);
        $rand_ids = array_slice($similar_artists_ids, 0, count($similar_artists_ids));
        $str_ids = implode(',', $rand_ids);

        // select BestSongs from Album_Best_Song
        $similar_song_voted = $this->getAlbumBestSong($user_id, $ids = $str_ids, $limit);

        return $similar_song_voted;
    }

    public function getBestSongsSimilar10($user_id, $limit = 0) {
        $logger = $this->getDI()->get("logger");
        $Best_songs_of_similar_top_10 = array();
        $top_10_artist_ids = "";

        // TOP 10 Artist
        $sql = "SELECT a.artist_id, a.artist_name, count(a.song_id) 
					FROM Song_User_Vote a
					INNER JOIN Album_Best_Song b ON a.song_id = b.id
					WHERE a.user_id = $user_id
					GROUP BY a.artist_id, a.artist_name
					ORDER BY count(a.song_id), b.general_score, b.hotttnesss DESC
					LIMIT 10";

        $top_10_artist = $this->db->fetchAll($sql, Phalcon\Db::FETCH_OBJ);
        // $top_10_artist = $this->modelsManager->executeQuery($sql)->toArray();
		
        if (is_array($top_10_artist) && count($top_10_artist)) {
            foreach ($top_10_artist as $item) {
                $top_10_artist_ids .= $item->artist_id . ",";
            }

            $top_10_artist_ids = rtrim(rtrim($top_10_artist_ids), ',');

            // get 30 best scored similar artists
            $sql = "SELECT GROUP_CONCAT(similar_artists_ids SEPARATOR ',') AS similar_artists_ids
					    FROM Artist_Similarity 
						WHERE artist_id IN (".$top_10_artist_ids.") 
						LIMIT 30";

            $similar_artists_ids = $this->db->fetchOne($sql, Phalcon\Db::FETCH_OBJ);

            $Best_songs_of_similar_top_10 = $this->getAlbumBestSong($user_id, $similar_artists_ids->similar_artists_ids, $limit);
        }

        return $Best_songs_of_similar_top_10;
    }

    //  NOT USED	
    public function getFriendsBestSongs() {
        $friends_best_song = array();

        $user_friends = $this->modelsManager->executeQuery("SELECT * FROM user_friends WHERE user1_id = $user_id");

        if (!empty($user_friends)) {
            array_walk($user_friends, function ($item, $key) {
                $user_friends_ids .= $item->user2_id . ",";
            });

            $sql = "SELECT artist_id, count(song_id) 
						FROM song_user_vote 
						WHERE user_id IN  (" . rtrim(rtrim($user_friends_ids), ',') . ") 
						GROUP BY artist_id
						ORDER BY count(song_id)
						LIMIT 20";

            $friends_best_artist = $this->modelsManager->executeQuery($sql);
            $this->session->set("friends_best_artist", $friends_best_artist);

            // Friends' Best songs
            $sql = "SELECT * FROM song 
                    WHERE artist_id IN (" . rtrim(rtrim($friends_best_artist['artist_id']), ',') . ") " .
                    "ORDER BY general_score
                    LIMIT 20 ";

            $friends_best_song = $this->modelsManager->executeQuery($sql);
        }

        return $friends_best_song;
    }

    public function getAffiliatedVotedSongs($user_id, $limit = 50) {
        $affiliated_voted_songs = array();
        $affiliated_voted_users = "";
/*
        $sql = "SELECT GROUP_CONCAT(song_id SEPARATOR ',') AS song_ids  
					 FROM Song_User_Vote
					 WHERE user_id = $user_id 
					 ORDER BY vote_date DESC";
        // LIMIT 1";

        $voted_songs = $this->db->fetchOne($sql, Phalcon\Db::FETCH_OBJ)->song_ids;
        // $this->session->set("last_song_voted", $last_song_voted);
        if (!$voted_songs)
            return $affiliated_voted_songs;

        $voted_songs = rtrim(rtrim($voted_songs), ',');
*/
        $sql = "SELECT user_id, COUNT(*) 
				FROM Song_User_Vote 
				WHERE song_id IN  (SELECT song_id FROM Song_User_Vote WHERE user_id = $user_id) 
                AND user_id <> $user_id 
                GROUP BY user_id
                HAVING COUNT(*) > 1";

        $res = $this->db->query($sql);

        if ($res->numRows()) {
            $res->setFetchMode(Phalcon\Db::FETCH_OBJ);
            
            while ($row = $res->fetch()) 
				$affiliated_voted_users .= $row->user_id . ',';				
			
            if ($affiliated_voted_users == "")
                return $affiliated_voted_songs;
            
            $affiliated_voted_users = rtrim(rtrim($affiliated_voted_users), ',');

            $sql = "SELECT b.id,
							b.song_name, 
							b.artist_id,
							b.artist_name,
							b.album_id,
							b.album_name,
							b.deezer_track_id,
							b.hotttnesss,
							b.votes_number,
							b.general_score,
							b.deezer_preview_url,
							b.album_visual,
							b.song_duration
						FROM Song_User_Vote a 
						LEFT JOIN User_Suggested_Songs S ON S.song_id = a.song_id AND S.user_id = $user_id 
						INNER JOIN Album_Best_Song b ON a.song_id = b.id
						WHERE a.user_id IN (".$affiliated_voted_users.")
						AND S.song_id IS NULL 
						ORDER BY a.vote_date DESC, b.album_date DESC, b.general_score DESC, b.hotttnesss DESC
						LIMIT $limit ";
			
			$result = $this->db->query($sql);
            $result->setFetchMode(Phalcon\Db::FETCH_OBJ);
            
            while ($row = $result->fetch()) 
				$affiliated_voted_songs[$row->id] = $row;			            
        }

        return $affiliated_voted_songs;
    }

    public function getBestSongsOfBestlastStylesVoted($user_id, $limit = 10) {
        $results = array();
        
        $sql = "SELECT
		SUM( b.style1_weight ) as style_score,
		b.style1_name as style,
		b.style1_id as style_id
		FROM
		Song_User_Vote a
		INNER JOIN
		Song b ON a.song_id = b.id and a.user_id = $user_id
		group by
		b.style1_name,
		b.style1_id
		UNION ALL
		SELECT
		SUM( b.style2_weight ) as style_score,
		b.style2_name as style,
		b.style2_id as style_id
		FROM
		Song_User_Vote a
		INNER JOIN
		Song b ON a.song_id = b.id and a.user_id = $user_id
		group by
		b.style2_name,
		b.style2_id
		UNION ALL
		SELECT
		SUM( b.style3_weight ) as style_score,
		b.style3_name as style,
		b.style3_id as style_id
		FROM
		Song_User_Vote a
		INNER JOIN
		Song b ON a.song_id = b.id and a.user_id = $user_id
		group by
		b.style3_name,
		b.style3_id
		ORDER BY style_score DESC
		LIMIT 10";

        $res = $this->db->query($sql);
        $res->setFetchMode(Phalcon\Db::FETCH_OBJ);

        if (!$res->numRows())
            return array();

        $arr = array();

        while ($row = $res->fetch()) {
            $arr[] = $row->style_id;
        }

        $user_10_best_styles = implode(',', $arr);

        $sql2 = "SELECT a.id,
                         a.song_name, 
                         a.artist_id,
                         a.artist_name,
                         a.album_id,
						a.album_name,
						a.deezer_track_id,
						a.hotttnesss,
						a.votes_number,
						a.general_score,
						a.deezer_preview_url,
						a.album_visual,
						a.song_duration
				 FROM Album_Best_Song a
				 LEFT JOIN User_Suggested_Songs S ON S.song_id = a.id AND S.user_id = $user_id 
				 WHERE a.style1_id IN (".$user_10_best_styles.")
				 AND S.song_id IS NULL
				 ORDER BY a.album_date DESC, a.general_score DESC, a.hotttnesss DESC
				 LIMIT $limit";

         $result = $this->db->query($sql2);
         $result->setFetchMode(Phalcon\Db::FETCH_OBJ);
            
         while ($row = $result->fetch()) 
			$results[$row->id] = $row;

        return $results;
    }

/*
 * function GetBestSongOfMyFavorite 
 * 
 * TODO : check if it's more effective with 2 queries 
 * 
 */
 
    public function GetBestSongOfMyFavorite($user_id, $limit = 0) {
        $results = array();
        // get my faved artists 
        
        
        $my_faved_artists = "";
        
        $query = "SELECT a.id,
                         a.song_name, 
                         a.artist_id,
                         a.artist_name,
                         a.album_id,
						a.album_name,
						a.deezer_track_id,
						a.hotttnesss,
						a.votes_number,
						a.general_score,
						a.deezer_preview_url,
						a.album_visual,
						a.album_date,
						a.song_duration
				FROM Album_Best_Song a
				INNER JOIN User_Fb_Musical_Activity b ON a.artist_id = b.artist_id AND b.user_id = $user_id
				LEFT JOIN User_Suggested_Songs S ON S.song_id = a.id AND S.user_id = $user_id 
				WHERE S.song_id IS NULL 
				AND a.album_year >= " . $this->start_year . 
				($this->min_hotttnesss ? " AND hotttnesss > " . $this->min_hotttnesss : "") . 
				" UNION
				SELECT a.id,
                         a.song_name, 
                         a.artist_id,
                         a.artist_name,
                         a.album_id,
						a.album_name,
						a.deezer_track_id,
						a.hotttnesss,
						a.votes_number,
						a.general_score,
						a.deezer_preview_url,
						a.album_visual,
						a.album_date,
						a.song_duration
				FROM Album_Best_Song a
				INNER JOIN User_Deezer_Artist c ON a.artist_id = c.artist_id AND c.user_id = $user_id
				LEFT JOIN User_Suggested_Songs S ON S.song_id = a.id AND S.user_id = $user_id 
				WHERE S.song_id IS NULL  
				AND a.album_year >= " . $this->start_year . 
				($this->min_hotttnesss ? " AND hotttnesss > " . $this->min_hotttnesss : "") .
				// " ORDER BY RAND() " .
				" ORDER BY album_date DESC, general_score DESC, hotttnesss DESC " .
				($limit ? " LIMIT $limit " :"") ;

        $songs = $this->db->fetchAll($query, Phalcon\Db::FETCH_OBJ);
		
		foreach ($songs as $song) {
			$results[$song->id] = $song;
		}
		
        return $results;
    }

    public function getPopularSongs($user_id, $limit = 30) {
			$results = array();
			
			$sql = "SELECT a.id,
						   a.song_name, 
						   a.artist_id,
						   a.artist_name,
						   a.album_id,
						a.album_name,
						a.deezer_track_id,
						a.hotttnesss,
						a.votes_number,
						a.general_score,
						a.deezer_preview_url,
						a.album_visual,
						a.song_duration
				FROM  Album_Best_Song a
				LEFT JOIN User_Suggested_Songs S ON S.song_id = a.id AND S.user_id = $user_id 
				WHERE S.song_id IS NULL
				ORDER BY a.album_year DESC, 
				a.general_score DESC, 
				a.hotttnesss DESC
				LIMIT $limit ";

			$result = $this->db->query($sql);
            $result->setFetchMode(Phalcon\Db::FETCH_OBJ);
            
            while ($row = $result->fetch()) 
				$results[$row->id] = $row;
        
			return $results;
    }
    
/* 
 * Suggestion functions for User_Filters_Search 
 * 
 */
	public function GetSongsByArtistSearch($user_id, $artist_id) {
		$similar_artists = "";
		
		$sql = "SELECT similar_artists_ids FROM Artist_Similarity WHERE artist_id = $artist_id";
		
		$res = $this->db->query($sql);
		
		if ($res->numRows()) {
			$res->setFetchMode(Phalcon\Db::FETCH_OBJ);
			$similar_artists = $res->fetch()->similar_artists_ids;			
		}
		
		$sql = "SELECT GROUP_CONCAT(id SEPARATOR ',') AS similar_artists_ids 
				FROM Artist 
				WHERE style1_id = (SELECT style1_id FROM Artist WHERE id = $artist_id) ";

		$res = $this->db->query($sql);
		
		if ($res->numRows()) {
			$res->setFetchMode(Phalcon\Db::FETCH_OBJ);			
			
			if ($similar_artists != "")
				$similar_artists .= ',' . $res->fetch()->similar_artists_ids;
			else 
				$similar_artists  = $res->fetch()->similar_artists_ids;	
		}
		
		$similar_artists .= ($similar_artists != "" ? ',' : '') . $artist_id;
		
		return $this->getAlbumBestSong($user_id, $similar_artists);		 		
	}
	

	public function GetSongsBySongSearch($user_id, $song_id) {
		$similar_artists = "";
		
		$res = $this->db->query("SELECT artist_id FROM Song WHERE id = $song_id");
		
		if (!$res)
			return array();
		
		$res->setFetchMode(Phalcon\Db::FETCH_OBJ);			
		$artist_id = $res->fetch()->artist_id;
		
		$sql = "SELECT similar_artists_ids 
				FROM Artist_Similarity 
				WHERE artist_id = $artist_id";
				
		$res = $this->db->query($sql);
		
		if ($res->numRows()) {
			$res->setFetchMode(Phalcon\Db::FETCH_OBJ);
			$obj = $res->fetch();
			$similar_artists = $obj->similar_artists_ids;
		}
		
		$sql = "SELECT GROUP_CONCAT(id SEPARATOR ',') AS similar_artists_ids 
				FROM Artist WHERE style1_id = (SELECT style1_id FROM Artist WHERE id = $artist_id)";
				
		$res = $this->db->query($sql);

		if ($res->numRows()) {
			$res->setFetchMode(Phalcon\Db::FETCH_OBJ);			
			
			if ($similar_artists != "")
				$similar_artists .= ',' . $res->fetch()->similar_artists_ids;
			else 
				$similar_artists  = $res->fetch()->similar_artists_ids;	
		}

		$similar_artists .= ($similar_artists != "" ? ',' : '') . $artist_id;
		
		return $this->getAlbumBestSong($user_id, $similar_artists);		
	}

	public function GetSongsByStylesSearch($user_id, $style_id) {

		$sql = "SELECT GROUP_CONCAT(DISTINCT id SEPARATOR ',') AS similar_artists_ids
				FROM Artist 
				WHERE style1_id = $style_id OR style2_id = $style_id OR style3_id = $style_id";
				
		$res = $this->db->query($sql);
		
		if ($res->numRows()) {
			$res->setFetchMode(Phalcon\Db::FETCH_OBJ);
			
			$similar_artists = $res->fetch()->similar_artists_ids;
			
			return $this->getAlbumBestSong($user_id, $similar_artists);
		}
				
		return array();
	}
}
