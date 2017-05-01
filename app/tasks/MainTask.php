<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// TODO : update Artist.hotttnesss

require ($config->application->libraryDir . "EchoNestApi.php");
require ($config->application->libraryDir . "DeezerApi.php");
require ($config->application->libraryDir . "SpotifyApi.php");
require ($config->application->libraryDir . "iTunesApi.php");

class MainTask extends \Phalcon\CLI\Task
{
    public function mainAction() {
         echo "\nThis is the default task and the default action \n";
    }
    
    public function importAction($type) {
	$args = func_get_args();
	array_shift($args);
	
	try {
         switch ($type)   {
             case "styles" : 
                 $this->importStyles();
                 break;
             case "artists" :
                 $this->importArtists($args);
                 break;
             case "topartists" :
                 $this->importTopArtists($args);
                 break;
             case "topartistsbygenre" :
                 $this->importTopArtistsByGenre($args);
                 break;
			case "songs" :
				 $this->importSongs($args);
				 break;
			 case "albums" :
				 $this->importAlbums();
				 break;	
             default : 
                  echo "no action";
                 break;
         }
	 } catch (ErrorException $e) {
		print($e->getMessage() . PHP_EOL);
	 }
    }
    
    public function updateAction($type) {
			$method = "update$type";
			
			if (method_exists($this, $method))
				$this->$method();
	}
	
	public function getAction($type) {
			$method = "get$type";
			
			if (method_exists($this, $method))
				$this->$method();		
	}
	
	public function updateAlbumBestSong() {
		// echo date('H:i:s') . " : Start update ABS" . PHP_EOL ;
		
		$albums = AlbumBestSong::find();
		
		foreach ($albums as $album) {
			try {
				$query = "SELECT *
						  FROM `Song` 
						  WHERE album_id = " . $album->album_id . 
						" ORDER BY general_score DESC
						  LIMIT 1 ";
						  
				$song = $this->db->fetchOne($query, Phalcon\Db::FETCH_OBJ);
				
				if ($album->id != $song->id) {
					$album->save(array("id" => $song->id,
						"song_name" => $song->song_name,
						"general_score" => $song->general_score,
						"hotttnesss" => $song->hotttnesss,
						"deezer_url" => $song->deezer_url, 
						"spotify_url" => $song->spotify_url,
						"deezer_track_id" => $song->deezer_track_id,
						"itunes_url" => $song->itunes_url,
						"itunes_previewurl" => $song->itunes_previewurl, 
						"youtube_url" => $song->youtube_url,
						"deezer_preview_url" => $song->deezer_preview_url,
						"votes_number" => $song->votes_number));
				} elseif ($album->general_score != $song->general_score) {
					$album->save(array(
						"general_score" => $song->general_score,
						"votes_number" => $song->votes_number
					));
				}
				
			} catch (Exception $e) {
				echo "ERROR:" . $e->getMessage(). PHP_EOL;
				
				continue;
			}
		}
		
		// echo date('H:i:s') . " : END update ABS" . PHP_EOL ;
	}	
	
    // import all styles from Echonest 
    private function importStyles() 
    {
        $styles = array();
        $inserted_rows = 0;
        
        try {
			$echonest = new EchoNestApi();
			$styles = $echonest->GetListTerms();
			
			if (!empty($styles)) {
				foreach ($styles as $val) {
					$musical_style = new MusicalStyle();
							  
					if ($musical_style->save(array("style_name" => $val->name)) == false) {
						echo "We can't store styles : \n";
						foreach ($musical_style->getMessages() as $message) {
							echo $message, "\n";
						}                                        
					} else {
						echo "Inserted style " . $val->name . PHP_EOL;
						$inserted_rows++;
					}
				}            
			}
		} catch (ErrorException $e) {
			 echo "ERROR:: " . $e->getMessage();
			 exit();		
		}
		
        echo "Inserted $inserted_rows \n";
        
    } 
    
    /* function importArtists
     * Get top 1000 artists for each style from EchoNest and insert them in our DB 
     * @param void
     * 
     * @return void
     * @TODO : import 10 styles in Artist table
     */
     
    private function importArtists()
    {
		global $console;
		$nb_artists = 0;
		$MAX_ARTISTS = 1000 * 1000;
		$start_style = NULL;
		
		$args = func_get_args();
		foreach ($args[0] as $arg) {
			@list($flag, $value) = explode('=', $arg);
			
			if ($flag == '--start-style') {
				$start_style = $value;
			}
			
			if ($flag == '--top-artists') {
				$top_artists = true;
			}
			
			if ($flag == '--missing-artists') {
				
			}
		}
				
        $echonest = new EchoNestApi();
		
		try {
			// load all styles from DB
			if (is_null($start_style))
				$resultset = MusicalStyle::find();
			else 
				$resultset = MusicalStyle::find(array(
				" style_name >= :style:",
				"bind" => array("style" => $start_style )
				));	
					
			$styles = array();
			foreach ($resultset as $row) {
				$styles[$row->id] = $row->style_name;	
			}
			
			$flipStyles = array_flip($styles);
			
			foreach ($styles as $style) {
				// if ($nb_artists > $MAX_ARTISTS) break;					
				echo ">>> Get artists for style " . $style . PHP_EOL;
									
				// get top 1000 artist for each style
				$topArtists = $echonest->GetArtistsByStyle($style, 500);				
				$inserted_rows = $this->insertArtists($topArtists, $flipStyles);				
				
				$nb_artists += $inserted_rows;								
			} // end foreach style
        } catch (ErrorException $e) {
					 echo "ERROR:: " . $e->getMessage() . PHP_EOL ;
					 echo ">>>Imported $nb_artists artists " . PHP_EOL;							 
					 exit();
		}
    }
    
    public function fetchArtists()
    {
		// get Artists from Artists_to_Fetch table 
		$artists = ArtiststoFetch::find();

		$resultset = MusicalStyle::find();

		$styles = array();
		foreach ($resultset as $row) {
				$styles[$row->id] = $row->style_name;	
		}
			
		$flipStyles = array_flip($styles);
		
		// foreach artists 
		foreach ($artists as $artist) {
			echo ">>>Get Echonest info for this artist " . $artist->artist_name . PHP_EOL;
			
			// get Echonest info for this artist 
			$data = $en->getInfoAboutArtist($artist->echonest_id);
			try {
				$this->db->begin();
				
				// insert this artist 			
				$artist_id = $this->insertArtists(array($data));
				
				if (!$artist_id) {
					echo "Could not insert artist " . $artist->echonest_id . PHP_EOL;
					continue;
				} else {
					echo "Inserted artist with id : " . $artist_id  . PHP_EOL;
					$newArtist = Artist::findById($artist_id);
				}
					
				// insert his songs 
				$songs = $en->GetSongsByArtist($newArtist->echonest_id);
				$this->insertArtistSongs($songs, $newArtist);
						
				// insert his albums 
				$this->insertAlbums($songs);
				
				// delete artist
				$artist->delete();
				
				$this->db->commit();
			} catch (Exception $e) {
				echo $e->getMessage() . PHP_EOL;
				
				$this->db->rollback();				
			}
		}
	}
	
	function insertArtist($artist) 	
	{	 
		
		
	}
	
    function importTopArtists() 
    {
       $echonest = new EchoNestApi();
	   $max_artists = 2000;	
	   $start = $end = $total = 0;
	   $results = 1000;
	   
	   try {
			$resultset = MusicalStyle::find();
					
			$styles = array();
			foreach ($resultset as $row) {
				$styles[$row->id] = $row->style_name;	
			}
			
			$flipStyles = array_flip($styles);
																
			// get top artists
			while ($start < $max_artists) {
				$topArtists = $echonest->GetTopArtists(null, $start, $results);
			
				$inserted_rows = $this->insertTopArtists($topArtists);
echo "Inserted rows : " . $inserted_rows . PHP_EOL;

				$start += $results;
			}
				
        } catch (ErrorException $e) {
					 echo "ERROR:: " . $e->getMessage() . PHP_EOL ;
					 echo ">>>Imported $start artists " . PHP_EOL;
							 
					 exit();
		}
		
		echo "Success : Imported $start artists " . PHP_EOL;
 	}
	
	function importTopArtistsByGenre() 
	{
		$en = new EchonestApi();
		// Our styles
		$resultset = MusicalStyle::find(array("order" => "votes_number DESC"));
		$styles = array();
		$top_styles = array();
		
		foreach ($resultset as $row) {
			$styles[$row->id] = $row->style_name;
			
			//v$top_styles[] = $row->style_name;
		}
		
		$flipStyles = array_flip($styles);		
		
		foreach ($styles as $style) {
			try {
				// get top artists for this genre 
				echo "Get top artists for genre : " . $style  . PHP_EOL;
				$artists = $en->getArtistsByStyle($style);
							
				// insert artists 
				$nb_rows = $this->insertArtists($artists, $flipStyles);
				
				echo "Inserted $nb_rows artists "  . PHP_EOL;
			}	catch (Exception $e) {
						print $e->getMessage() . PHP_EOL .  $e->getTraceAsString();
						continue;
			}	
		}	
	}
	
    /* function importSongs
     * Retrieve a list of songs for each artist and insert them in Song table
     * @param flag --artist-id=xxx optional param for finding artist starting from this artist_id
     * 
     * @return void
     * TODO : wrap EN request in a try/catch block and check error code
     */
    
    public function importSongs()
    {
		// parse arguments
		$args = func_get_args();
		foreach ($args[0] as $arg) {
			@list($flag, $value) = explode('=', $arg);
			
			if ($flag == '--artist-id') {
				$start_artist_id = (int)$value;
			}
		}

		$connection = $this->getDI()->getShared("db");		
		$echonest = new EchoNestApi();
		$values		= array();		
		$max_songs  = 1000 * 1000;
		$total_songs = 0;
			
/*		
		$query  = "SELECT * FROM Artist 
				   WHERE status = 0 ";
								   	
		$result = $this->db->query($query);
				
		if (!$result)
			die("No Artists found");
		
		$result->setFetchMode(Phalcon\Db::FETCH_OBJ);	
*/			
		// get all artists 

		if (isset($start_artist_id))
			$artists = Artist::find(array("id > :start_artist_id:", "bind" => array("start_artist_id" => $start_artist_id)));
		else
			$artists = Artist::find("status = 0");
			
			// while ($artist = $result->fetch()) {
			foreach ($artists as $artist) {
				try {
					if ($total_songs > $max_songs) 
						break;
					
					echo ">>> Get songs for artist: " . $artist->name . ", #" . $artist->echonest_id . PHP_EOL ;	
					$songs = $echonest->GetSongByArtist($artist->echonest_id);
					
					$this->insertSongs($songs, $artist);
						
					// UPDATE Artist SET status = 1
					$connection->execute("UPDATE Artist SET status = 1 WHERE id = " . $artist->id);
					$total_songs += count($songs);
				} 	catch (\Exception $e) {
						 echo "ERROR:: " . $e->getMessage() . PHP_EOL ;
						 $trace = $e->getTrace(); 
						 print_r($trace[0]);
						 continue;
				}
			} // end foreach artists
	} 
	
	public function insertArtistSongs($songs, $artist) 
	{
		$connection = $this->getDI()->getShared("db");
		
		$fields = "`echonest_id`, `song_name`, `artist_id`, `artist_name`,`country`, `city`, `duration`, `hotttnesss`, `style1_id`, `style1_name`, `style1_weight`, `style1_score_weighted`, `style2_id`, `style2_name`, `style2_weight`, `style2_score_weighted`, `style3_id`, `style3_name`, `style3_weight`, `style3_score_weighted`, `style4_id`, `style4_name`, `style4_weight`, `style4_score_weighted`, `style5_id`, `style5_name`, `style5_weight`, `style5_score_weighted`, `style6_id`, `style6_name`, `style6_weight`, `style6_score_weighted`, `style7_id`, `style7_name`, `style7_weight`, `style7_score_weighted`, `style8_id`, `style8_name`, `style8_weight`, `style8_score_weighted`, `style9_id`, `style9_name`, `style9_weight`, `style9_score_weighted`, `style10_id`, `style10_name`, `style10_weight`, `style10_score_weighted`, `deezer_track_id`, `album_id`, `album_name`, `other_album_ids`, `spotify_url`, `spotify_album_id`, `itunes_url`, `deezer_preview_url`";
		$insertCLAUSE = "INSERT IGNORE  INTO `Song`($fields) VALUES " ;		

		// generate placeholders
		$pattern = "/`(\w)+`/i";
		$replace = "?";
		$valuesCLAUSE = '(' . preg_replace($pattern,$replace, $fields) . '),';

		$duplicates = array();
		$values		= array();
		$sqlINSERT = $insertCLAUSE;
		// TODO : change method to use $songs 
				
				if (!empty($songs)) {
					foreach ($songs as $song) {
						if (is_array($song->song_hotttnesss)) 
							$song->song_hotttnesss = array_shift($song->song_hotttnesss);
							
						// check for duplicates using the song_hotttnesss
						if (!in_array($song->song_hotttnesss, $duplicates )) 
							$duplicates[$song->title] = $song->song_hotttnesss;
						else 
							continue;
						
						$sqlINSERT .= $valuesCLAUSE;
												
						// fill in city & country 
						$city = $country = "";
						
						if (isset($song->artist_location->location)) {
							$arr = explode(',', $song->artist_location->location);
							if (count($arr) > 1) {
								list($city, $country) = $arr;
							} else {
								$city 		= "";
								$country 	= $arr[0];
							}
						}
							
						array_push($values, $song->id, $song->title, $artist->id, $song->artist_name, $country, $city, $song->audio_summary->duration, $song->song_hotttnesss);
						
						// add the styles inherited from Artist 
						for ($n = 1; $n <= 10; $n++) {
							array_push($values, $artist->{"style" . $n. "_id"}, $artist->{"style" . $n. "_name"}, $artist->{"style" . $n . "_weight"}, $artist->{"style" . $n . "_score_weighted"});
						}
						
						// Tracks (Deezer/Spotify) 
						$track_id = $main_album_id = $album_name = $spotify_uri = $spotify_album_id = $iTunes_url = $deezer_preview_url = null;
						$other_albums = array();						
						if (!empty($song->tracks)) {
							// Group tracks by catalog
							$tracks = array();
							array_walk($song->tracks, function($val, $key) use (&$tracks) {
								$tracks[$val->catalog][] = $val;
							});
							
							// Deezer
							if (isset($tracks["deezer"])) 
								foreach ($tracks["deezer"] as $key => $track) {
									$track_id   = isset($track->foreign_id) ? substr(strrchr($track->foreign_id, ':'), 1) : NULL;
									$album_id   = isset($track->foreign_release_id) ? substr(strrchr($track->foreign_release_id, ':'), 1) : NULL;
									if (is_null($album_id)) continue;
								
									if (is_null($main_album_id)) {
										$main_album_id 		= $album_id;
										$album_name 		= isset($track->album_name) ? $track->album_name : NULL;
										
										$spotify_album_id 	= @$tracks["spotify-WW"][0]->foreign_release_id;
										$spotify_album_id   = $spotify_album_id ?  substr(strrchr($spotify_album_id, ':'), 1) : null;
											
										$spotify_track_id 	= @$tracks["spotify-WW"][0]->foreign_id;
										$spotify_uri  		= $spotify_track_id ? "spotify:track:" . substr(strrchr($spotify_track_id, ':'), 1) : NULL;
										$iTunes_url 		= iTunes\getTrackURL($song->title, $song->artist_name);
										$deezer_preview_url = DeezerApi::getTrackInfo($track_id, "preview");
									} else {
										$other_albums[] = $album_id;
									}
								}						
						}	
						array_push($values, $track_id, $main_album_id, $album_name, join(',', $other_albums), $spotify_uri, $spotify_album_id, $iTunes_url, $deezer_preview_url);
				} // end foreach songs
				
				$connection->execute(rtrim($sqlINSERT, ','), $values);
				echo PHP_EOL . ">>>Inserted " . $connection->affectedRows() . "songs "  . PHP_EOL;
		}
	}
	
	private function updateSongs()
	{	
		$songs = Song::find("itunes_url IS NOT NULL");
		
		foreach ($songs as $song) {
			$song->itunes_previewurl = iTunes\getPreviewURL($song->song_name,  $song->artist_name);
			
			if (!$song->save())
				$song->error();
		}
		
	}
	
    /* function importAlbums
     * 
     * @param void
     * 
     * @return void
     * 
     */
	
	public function insertSongsAlbums($songs)
	{
		$echonest = new EchonestApi();
		
		$current_artist_id = 0;
		global $albums ;
		$albums = array();
		$max_songs = 1000 * 1000;
		$max_artists = 4000;
		$nb_artists  = 0;
		
		function album_exists($title) {
				global $albums;

				if (empty($albums))
					return NULL;

				foreach ($albums as $album) 
					if (strcasecmp($album->album_name, $title) === 0) {
						return $album;
					}
				
				return NULL;
		}

		foreach ($songs as $song) {
		try {
			$album_id  = $song->album_id;
			
			if (!isset($albums[$album_id]))	 {
				$album = DeezerApi::getAlbumInfo($album_id);
				if (is_null($album)) continue;
				// check if this album already exists, but with another id 
				if ($old_album = album_exists($album->title)) {
						// update song
						echo "old album id " .	 $old_album->id . PHP_EOL;
						$song->save(array("album_id" => $old_album->id, "album_name" => $old_album->album_name));
						continue;
				}
				
				$albums[$album_id] = new Album();
				$albums[$album_id]->assign(array("id" => $album_id, 
													"album_date" => $album->release_date,
													"album_year" => strstr($album->release_date, '-', true),
													"album_name" => $album->title,
													"artist_id"  => $song->artist_id,
													"artist_name" => $song->artist_name,
													"album_visual" => $album->cover,
													"deezer_uri"   => $album->link,
													"spotify_uri"  => Spotify\getAlbumURL($song->spotify_album_id),
													"itunes_uri"   => iTunes\getAlbumURL(@$album->title, $song->artist_name)));
				// add the styles inherited from Song 
				for ($n = 1; $n <= 10; $n++) {
					$albums[$album_id]->assign(array("style" . $n. "_id" => $song->{"style" . $n. "_id"}, 
												"style" . $n. "_name" 	=> $song->{"style" . $n. "_name"}, 
												"style" . $n . "_weight" => $song->{"style" . $n . "_weight"}, 
												"style" . $n . "_score_weighted" => $song->{"style" . $n . "_score_weighted"}));
				}
			} else 
				$album = $albums[$album_id];
				
			// update Song if album_name not set		
			if (is_null($song->album_name)) {
				if (!$song->save(array("album_name" => isset($album->album_name) ? $album->album_name : $album->title)))
					$song->error();
			}
		 } catch (Exception $e) {
					 echo "ERROR:: " . $e->getMessage() . PHP_EOL ;
					 $trace = $e->getTrace(); 
					 print_r($trace[0]);
					 continue;			 
		 }
		} // end foreach songs of this artist		
		
		if (!empty($albums)) {
			// INSERT $albums INTO Album table
			foreach ($albums as $album) 
				if (!$album->save())
				  $album->error();
				else 
					echo "Am salvat album id : " . $album->id . ", name : " . $album->album_name . PHP_EOL;

		}		
	}
	
	
	private function importAlbums($songs)
	{
		$echonest = new EchonestApi();
		
		$current_artist_id = 0;
		global $albums ;
		$albums = array();
		$max_songs = 1000 * 1000;
		$max_artists = 4000;
		$nb_artists  = 0;
		
		function album_exists($title) {
				global $albums;

				if (empty($albums))
					return NULL;

				foreach ($albums as $album) 
					if (strcasecmp($album->album_name, $title) === 0) {
						return $album;
					}
				
				return NULL;
		}

		// $songs = Song::find(array("order" => "id DESC"));
		
		foreach ($songs as $song) {
			try {
			// if change of the artist
			if ($song->artist_id != $current_artist_id) {
				$current_artist_id = $song->artist_id;
				echo "CHANGE artist id : " . $current_artist_id . PHP_EOL;
				
				if (!empty($albums)) {
					// INSERT $albums INTO Album table
					foreach ($albums as $album) 
						if (!$album->save())
						  $album->error();
						else 
							echo "Am salvat album id : " . $album->id . ", name : " . $album->album_name . PHP_EOL;

					$albums = array();
				}
				$nb_artists++;
				
				if ($nb_artists > $max_artists)
					break;
			}
			
			$album_id  = $song->album_id;
			
			if (!isset($albums[$album_id]))	 {
				$album = DeezerApi::getAlbumInfo($album_id);
				if (is_null($album)) continue;
				// check if this album already exists, but with another id 
				if ($old_album = album_exists($album->title)) {
						// update song
						echo "old album id " .	 $old_album->id . PHP_EOL;
						$song->save(array("album_id" => $old_album->id, "album_name" => $old_album->album_name));
						continue;
				}
				
				$albums[$album_id] = new Album();
				$albums[$album_id]->assign(array("id" => $album_id, 
													"album_date" => $album->release_date,
													"album_year" => strstr($album->release_date, '-', true),
													"album_name" => $album->title,
													"artist_id"  => $song->artist_id,
													"artist_name" => $song->artist_name,
													"album_visual" => $album->cover,
													"deezer_uri"   => $album->link,
													"spotify_uri"  => Spotify\getAlbumURL($song->spotify_album_id),
													"itunes_uri"   => iTunes\getAlbumURL(@$album->title, $song->artist_name)));
				// add the styles inherited from Song 
				for ($n = 1; $n <= 10; $n++) {
					$albums[$album_id]->assign(array("style" . $n. "_id" => $song->{"style" . $n. "_id"}, 
												"style" . $n. "_name" 	=> $song->{"style" . $n. "_name"}, 
												"style" . $n . "_weight" => $song->{"style" . $n . "_weight"}, 
												"style" . $n . "_score_weighted" => $song->{"style" . $n . "_score_weighted"}));
				}
			} else 
				$album = $albums[$album_id];
				
			// update Song if album_name not set		
			if (is_null($song->album_name)) {
				if (!$song->save(array("album_name" => isset($album->album_name) ? $album->album_name : $album->title)))
					$song->error();
			}
		 } catch (Exception $e) {
					 echo "ERROR:: " . $e->getMessage() . PHP_EOL ;
					 $trace = $e->getTrace(); 
					 print_r($trace[0]);
					 continue;			 
		 }
		} // end foreach songs of this artist		
	}
	
	private function updateDeezerUrls() {
		$songs = Song::find("deezer_track_id IS NOT NULL AND deezer_preview_url IS NULL");
		
		foreach ($songs as $song) {
			try {
				if (is_null($track  = DeezerApi::GetTrackInfo($song->deezer_track_id)))
					continue;
					
				$song->deezer_preview_url = $track->preview;
				$song->save();			
			} catch (ErrorException $e) {
				echo $e->getMessage() . PHP_EOL;
				continue;
			}
		}
		
		return true;
	}
	
	
	/*
	* Insert Artists into DB
	*/
	function insertArtists($artists, $flipStyles) 
	{		
		$connection = $this->getDI()->getShared("db");

		// generate SQL for Insert Artists
        $sqlINSERT = "INSERT IGNORE INTO `Artist`(`echonest_id`, `name`,  `country`, `city`, `hotttnesss`, `visual_urls`, ";
        for ($i = 1; $i <= 10; $i++)  
			$sqlINSERT .= "`style{$i}_id`, `style{$i}_name`, `style{$i}_weight`,";
		
		$sqlINSERT = trim($sqlINSERT, ',');
		$sqlINSERT .=  ") VALUES " ;

		$valuesCLAUSE = "";

		foreach ($artists as $artist) {
					$valuesCLAUSE .= "('" . $artist->id . "','" .  
									addslashes($artist->name) . "','".  
									addslashes(@$artist->artist_location->country) . "','" .  
									addslashes(@$artist->artist_location->city) . "'," . 
									$artist->hotttnesss . ",";
									
					// image_urls
					$visual_urls = NULL;
					if (isset($artist->images)) {
							$images = array();
							
							// keep only 3 images
							array_splice($artist->images, 3); 
							
							foreach ($artist->images as $image) {
								$images[] = @$image->url;
							}
							
							$visual_urls = implode(' ', $images);
					}
					
					$valuesCLAUSE .= "'" . addslashes($visual_urls) . "',";
					
					for ($n = 0; $n < 10; $n++) {								
						$termName 	= isset($artist->terms[$n]->name) ? $artist->terms[$n]->name : '';
						$termWeight = isset($artist->terms[$n]->weight) ? $artist->terms[$n]->weight : 'NULL';
						$styleId 	= isset($flipStyles[$termName]) ? $flipStyles[$termName] : 0;

						$valuesCLAUSE .= $styleId . ",'" . addslashes($termName) . "',". $termWeight . ",";							
					}
				   
				   $valuesCLAUSE = rtrim($valuesCLAUSE, ',') . '),';
		}
		
		$valuesCLAUSE = rtrim($valuesCLAUSE, ',');

		$connection->execute($sqlINSERT . $valuesCLAUSE);
		
		if (count($artists) == 1) 
			return $connection->lastInsertId();
		else 	
			return $connection->affectedRows();
	}
	
	function getSimilarArtists() 
	{
		$echonest = new EchoNestApi();
		$connection = $this->getDI()->getShared("db");

		$artists = Artist::find();
			
		foreach ($artists as $artist) {
			try {
				$similars = $echonest->getSimilarArtists($artist->echonest_id);
				
				if (!is_null($similars)) {
					$str_similar_ids = "";
					foreach ($similars as $smartist) {
						$our_id = $this->translateEchonestID($smartist->id);
						// if not found, store it into Artists_to_Fetch
						if (!$our_id) {
							$insert = "INSERT INTO Artists_to_Fetch(artist_name, echonest_id) VALUES (? , ?)";
							$this->db->execute($insert, array($smartist->name, $smartist->id));
							continue;
						}	
						
						$str_similar_ids .= $our_id . ','; 
					}
					
					// insert them into User_Similar_Artists
					$new_entry = new ArtistSimilarity();
					$new_entry->assign(array(
						"artist_id" => $artist->id, 
						"artist_name" => $artist->name,
						"similar_artists_ids" => rtrim($str_similar_ids, ',')));
								
					if (!$new_entry->save()) {
						echo $new_entry->getMessages()[0] . PHP_EOL;
					}
					
				}
			} catch (Exception $e) {
				echo $e->getMessage() . PHP_EOL;
				continue;				
			}
		}
	}
	
	function translateEchonestID($id) {
		$a = $this->db->query("SELECT id FROM Artist WHERE echonest_id = '" . $id . "'");
		
		if ($a->numRows())
			return $a->fetchArray()["id"];
		else
			return 0;
	}

	function insertTopArtists($artists) {
		$sql = "INSERT INTO top_artists(artist_name, echonest_id, hotttnesss) VALUES ";
		$values = "";
		
		foreach ($artists as $artist) {
			$values .= "('" .  addslashes($artist->name) ."', '" . $artist->id ."'," . $artist->hotttnesss . "),";
		}
		
		$query = $sql . rtrim($values, ',');
		
		$this->db->execute($query);
		
		return $this->db->affectedRows();
	}
}


