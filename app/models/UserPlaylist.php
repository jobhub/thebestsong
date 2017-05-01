<?php




class UserPlaylist extends \Phalcon\Mvc\Model
{
	use Error;
    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var integer
     */
    public $user_id;
     
    /**
     *
     * @var string
     */
    public $playlist_name;
     
    /**
     *
     * @var string
     */
    public $song_ids;
     
    /**
     *
     * @var integer
     */
    public $deezer_external_id;
     
    /**
     *
     * @var integer
     */
    public $spotify_external_id;
     
    /**
     *
     * @var string
     */
    public $status;
     
    /**
     *
     * @var string
     */
    public $update_date;
     
    /**
     *
     * @var string
     */
    public $create_date;
    
    public function initialize()
    {
		$this->setSource('User_Playlist');
		$this->skipAttributes(array('update_date'));
	
    }
    public function saveSong($song_id)
    {
		// save song 
		if (!is_null($this->song_ids)) 
			$arr = explode(',', $this->song_ids);		
		else 
			$arr = array();
			
		$arr[] = $song_id;		
		$this->song_ids = implode(',', $arr);
		
		return $this->save();

	}
    public static function getCurrentPlaylist($user_id)
    {
		$user = User::findFirstById($user_id);
		
		$pl = self::findFirst(array("user_id = :user_id: AND status = 'open'", 
				"bind" => array("user_id" => $user_id), 
				"order" => "id DESC"));
		
		if (!$pl) {
			$pl = self::registerNewPlaylist($user_id, $user->email);
		}
		
		return $pl;	
	}
	
	public static function registerNewPlaylist($user_id, $user_email) 
	{
			// first delete empty playlists 
			$modelsManager = Phalcon\Di::getDefault()->get("modelsManager");
			
			$modelsManager->executeQuery("DELETE FROM UserPlaylist WHERE user_id = $user_id AND song_ids IS NULL ");

			$pl = new UserPlaylist();
			
			$rows = self::findByUserId($user_id);
			$nb_playlists = $rows->count();
			
			$pl->assign(array(
			"user_id" 				=> $user_id, 
			"playlist_name" 		=> "Playlist#" . (int)($nb_playlists + 1),
			"create_date"			=> new \Phalcon\Db\RawValue('now()'),
			"status" 				=> 'open'));
			
			if (!$pl->save()) {
				foreach ($pl->getMessages() as $msg) 
					echo $msg . PHP_EOL;
				
				return false;	
			}
			
			return $pl;
	}
	
	public static function getPlayedSongs($user_id) 
	{
		$pl = self::findFirst(array("user_id = :user_id: AND status = 'open'", 
				"bind" => array("user_id" => $user_id), 
				"order" => "id DESC"));
		
		if ($pl)
			return $pl->song_ids;

			return false;
	}
/*
     * Function getUserPlaylists
     * 
     * @param $user_id 
     * 
     * @return array the user's playlists
     * 
*/
	
	public static function getUserPlaylists($user_id) 
    {
		$playlists = array();
		
		$modelsManager = Phalcon\Di::getDefault()->get("modelsManager");
				
        $sql = "SELECT id,
                       playlist_name, 
                       song_ids
                FROM UserPlaylist
                WHERE user_id = " . $user_id  ;
   
        $result = $modelsManager->executeQuery($sql);
        $playlists = $result->toArray();
        
        for ($i = 0; $i < count($playlists); $i++) {
            if ($playlists[$i]['song_ids'] != "") {
            $sql = "SELECT Song.id,
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
                    WHERE Song.deezer_preview_url IS NOT NULL
                    AND Song.id IN (".$playlists[$i]['song_ids'].") 
                    ORDER BY FIELD(Song.id,".$playlists[$i]['song_ids'].") DESC";

            $result = $modelsManager->executeQuery($sql);
            $playlists[$i]['songs'] = $result->toArray();
            
            
            $sql2 = "SELECT ROUND(SUM(duration)/60) AS totalduration
                      FROM Song 
                      WHERE deezer_preview_url IS NOT NULL
                      AND id IN (".$playlists[$i]['song_ids'].")";
             
            $result = $modelsManager->executeQuery($sql2);
            $durations = $result->toArray();
            $playlists[$i]['totalduration'] = $durations[0]['totalduration'];
			} else {
				$playlists[$i]['songs'] = array();
				$playlists[$i]['totalduration'] = 0;		
			}
        }
		
		return $playlists;	
	}

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'playlist_name' => 'playlist_name', 
            'song_ids' => 'song_ids', 
            'deezer_external_id' => 'deezer_external_id', 
            'spotify_external_id' => 'spotify_external_id', 
            'status' => 'status', 
            'update_date' => 'update_date', 
            'create_date' => 'create_date'
        );
    }

}
