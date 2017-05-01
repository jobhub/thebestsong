<?php

Class EchoNestApi
{
	// public $api_key = 'C9CETUOKK3YAUKKW8';
	public $api_key = 'SBPKL3LQ04MM2QHKW';
	// public $api_key = '3AG7XTMTSROUMQKPY';
	
	public $deezerToken = 'frDt6SeZc4532afa8e58caacfL0Vkmr532afa8e58cechNbUzT';
	
	public $description = '';
	public $terms = array('style', 'mood'); 
	
        
        // @return array The list of all styles
        // @return NULL if error 
        function GetAllGenre()
        {
            $api_query = 'http://developer.echonest.com/api/v4/genre/list?api_key='. $this->api_key .'&bucket=description&format=json&results=10000';
            $content = file_get_contents($api_query, true);

            $genre_result = json_decode($content);
            if ($genre_result->response->status->message == 'Success') {
                    // echo "<pre>";
                    echo "Found " . count($genre_result->response->genres) . " styles " . PHP_EOL;                    
                    // echo "</pre>";
            } else {
                echo ('ErrorApi') . PHP_EOL;
                return NULL;
            }
            
            return $genre_result->response->genres;
        }
		
		
		function GetListTerms($term = "style")
		{
		
			$api_query = 'http://developer.echonest.com/api/v4/artist/list_terms?api_key='. $this->api_key .'&format=json&type=' .$term;
			
			$content = file_get_contents($api_query, true);
			$list_term_result = json_decode($content);
			
/*			
			// insert style in song_style table			
			foreach($list_term_result->response->terms as $k)
			{
				$style_name = $k->name;
				echo 	$style_name ."<br>";
				
				$sql = "insert into song_style (style_name) VALUES ('". $style_name ."')";
				$res = mysql_query ($sql, $connexion);
				
				
			}

			echo "<pre>";
			print_r($list_term_result->response->terms);
			echo "</pre>";

*/			
			
			return $list_term_result->response->terms;			
		}
		
		function GetTopTerms()
		{
			$api_query = "http://developer.echonest.com/api/v4/artist/top_terms?api_key=" . $this->api_key. "&format=json&results=100";
			echo $api_query . PHP_EOL;
			$content = file_get_contents($api_query, true);
			$list_term_result = json_decode($content);

			return $list_term_result->response->terms;

		}
		
		function GetArtistsByGenre($genre, $limit = 1000)
		{			
			$sort 		= 'hotttnesss-desc';
			//$genre 			= 'grunge';
			$description	= '';
			$style 			= '';
			$start 			= 0;
			$results		= 100;
			$extraBucket 	= '&bucket=images';
			$total_found = $nb_found  = 0;
                        
			$ArtistList     = array();

		        do { 
                            // search artist by genre
                            $api_query_artist_by = 'http://developer.echonest.com/api/v4/artist/top_hottt?api_key='. $this->api_key .'&genre='. urlencode($genre).'&bucket=genre&bucket=terms&bucket=hotttnesss&bucket=discovery&bucket=doc_counts&bucket=urls&bucket=hotttnesss_rank&bucket=artist_location' . $extraBucket. '&sort='.$sort.'&start='. $start .'&results='.$results;
                            echo $api_query_artist_by . PHP_EOL;
                            
                            $content = file_get_contents($api_query_artist_by, true);

                            $result = json_decode($content);

                            $artists = $result->response->artists;
							$nb_found = count($artists);
							
                            if ($nb_found)
                            {
                                array_splice($ArtistList, count($ArtistList), 0, $artists);                                
                            }
                            
                            echo "Read $nb_found artists \n";
                            
                            $start += $nb_found;
                            $total_found += $nb_found;
                            sleep(6);
                                                        
                } while ($start < $limit && $nb_found) ;
                        
                          return $ArtistList;
                        
//                        echo "<pre>";
//                        print_r($ArtistList);
//                        echo "</pre>";
                        
			// search artist by mood/ style
			//$api_query_artist_by = 'http://developer.echonest.com/api/v4/artist/search?api_key='. $this->api_key .'&style= '. $style .'&mood='. $mood .'&sort='.$sort.'&start='. $start .'&results='.$end;

			// search artist by style and area
			//$api_query_artist_by = 'http://developer.echonest.com/api/v4/artist/search?api_key='. $this->api_key .'&style= '. $style.'&artist_location='. $area .'&bucket=artist_location&sort='.$sort.'&start='. $start .'&results='.$end;			
			
			// search artist by genre and area
			//$api_query_artist_by = 'http://developer.echonest.com/api/v4/artist/search?api_key='. $this->api_key .'&genre='. $genre.'&artist_location='. $area .'&bucket=artist_location&sort='.$sort.'&start='. $start .'&results='.$end;
		}		

		/* GetArtistsByStyle
		 * returns an array of artists for the passed 
		 * @param $style the style (null if we want top artists, ignore style)
		 * @param $limit how many artists to retrieve for it
		 * 
		 * @return array the artists found
		 * 
		 */
		function GetArtistsByStyle($style = null, $start = 0, $limit = 1000)
		{			
			$sort 		= 'hotttnesss-desc';
			$start 			= $start;
			$results		= 100;
			$extraBucket 	= '&bucket=images';
			$total_found = $nb_found  = 0;
                        
			$ArtistList     = array();
                        
		    do { 
				// search artist by style
				$api_query_artist_by = 'http://developer.echonest.com/api/v4/artist/top_hottt?api_key='. $this->api_key . (!is_null($style) ?  '&genre='. urlencode($style) : '' ). '&bucket=terms&bucket=hotttnesss&bucket=hotttnesss_rank&bucket=artist_location' . $extraBucket . '&start='. $start .'&results='.$results;
				echo $api_query_artist_by . PHP_EOL;
					
				$content = file_get_contents($api_query_artist_by, true);				
				$this->checkLimits();
				$result = json_decode($content);

				$artists = $result->response->artists;
				$nb_found = count($artists);
				
				if ($nb_found)
				{
					array_splice($ArtistList, count($ArtistList), 0, $artists);                                
				}
				
				echo "Read $nb_found artists \n";
				
				$start += $nb_found;
				$total_found += $nb_found;
                                                        
                } while ($start < $limit && $nb_found == $results) ;
                        
                return $ArtistList;                        
		}
		
		function GetTopArtists($style = null, $limit = 1000) 
		{
			$sort 			= 'hotttnesss-desc';
			$start 			= 0;
			$results		= 100;
			$extraBucket 	= '&bucket=images';
			$total_found = $nb_found  = 0;
                        
			$ArtistList     = array();
                        
		    do { 
				// search artist by style
				$api_query_artist_by = 'http://developer.echonest.com/api/v4/artist/top_hottt?api_key='. $this->api_key . (!is_null($style) ?  '&genre='. urlencode($style) : '' ). '&bucket=hotttnesss&bucket=hotttnesss_rank&start='. $start .'&results='.$results;
				echo $api_query_artist_by . PHP_EOL;
					
				$content = file_get_contents($api_query_artist_by, true);
				if (strstr($http_response_header[10], ':', true) == "X-Ratelimit-Limit")   {
					$rate_limit = (int)substr(strrchr($http_response_header[10], ":"), 1);
					
					$sleep_sec = 60/$rate_limit;
					echo "sleep $sleep_sec" . PHP_EOL;
					sleep($sleep_sec);
				};
				

				$result = json_decode($content);

				$artists = $result->response->artists;
				$nb_found = count($artists);
				
				if ($nb_found)
				{
					array_splice($ArtistList, count($ArtistList), 0, $artists);                                
				}
				
				echo "Read $nb_found artists \n";
				
				$start += $nb_found;
				$total_found += $nb_found;
                                                        
                } while ($start < $limit && $nb_found == $results) ;
                        
                return $ArtistList;                        
		}
		
		function GetAlbumByArtistAndSong($artist, $title)
		{
			
			$echoNest_to_Deezer_api = 'http://developer.echonest.com/api/v4/song/search?api_key='. $this->api_key .'&format=json&results=1&artist='. $artist .'&title='. $title .'&bucket=id:deezer&bucket=tracks&limit=true';
			$content = file_get_contents($echoNest_to_Deezer_api, true);
			$result = json_decode($content);
			
			$trackList = $result->response->songs[0]->tracks;
			
			if (!empty($trackList))
			{
			
				foreach ($trackList as $k)
				{

					$deezer_id = str_replace('deezer:track:','', $k->foreign_id);
					$deezer_api_request = 'http://api.deezer.com/track/'.$deezer_id;
					$content = file_get_contents($deezer_api_request, true);
					$result = json_decode($content);
				
					echo "<pre>";
						print_r($result->album);
					echo "</pre>";
				}
			}	
			
			echo "<pre>";
			print_r($trackList);
			echo "</pre>";
				
		}
		
		
		function GetSongByArtist($artist_id)
		{
			global $http_response_header;
			$sortDesc 	= 'song_hotttnesss-desc';
			$end 		= 100;

			
			$api_query = 'http://developer.echonest.com/api/v4/song/search?api_key='. $this->api_key .'&format=json&results='.$end.'&bucket=audio_summary&bucket=artist_location&bucket=song_hotttnesss&bucket=id:deezer&&bucket=id:spotify-WW&bucket=tracks&limit=true&artist_id='. $artist_id .'&sort='.$sortDesc;
echo $api_query . PHP_EOL;
			$content = file_get_contents($api_query, true);
			$this->checkLimits();
			
			$result = json_decode($content);							
		
			$listSong = $result->response->songs;
/*
			echo "<pre>";
			print_r($listSong);
			echo "</pre>";							
*/
			return $listSong;
		}
		
		function GetSongInfo($song_id) 
		{			
			
			$api_query = "http://developer.echonest.com/api/v4/song/profile?api_key=" . $this->api_key. "&format=json&bucket=tracks&bucket=id:deezer&bucket=id:spotify-WW&limit=true&id=" . $song_id;
echo $api_query . PHP_EOL;
			
			$content = file_get_contents($api_query, true);
			$result = json_decode($content);
				
			return $result->response->songs;
		}
		
		
		function GetSimilarArtists($artist_id = "", $artist_name = "", $results = 20)
		{
			$start = 0;
			// $minimum_affinity = '0.7';
			// $min_hotttnesss = '0.7';
			
			$api_query = 'http://developer.echonest.com/api/v4/artist/similar?api_key='. $this->api_key .
			($artist_id != "" ? '&id='.$artist_id  : '&name='.urlencode($artist_name)) .'&format=json&bucket=familiarity&bucket=hotttnesss_rank&bucket=hotttnesss&start='. $start .'&results='.$results;
			
			echo $api_query . PHP_EOL;
						
			$content = file_get_contents($api_query, true);
			$this->checkLimits();
			
			$result = json_decode($content);							
			
			if (isset($result->response->artists)) 
				return $result->response->artists;
			else 
				return null;
			// $logger->log(print_r($listArtistSimilar, true));
						
		}

		function checkLimits()
		{
			global $http_response_header;
			
			if (strstr($http_response_header[10], ':', true) == "X-Ratelimit-Limit")   {
				$rate_limit = (int)substr(strrchr($http_response_header[10], ":"), 1);
				
				$sleep_sec = 60/$rate_limit;
				echo "sleep $sleep_sec" . PHP_EOL;
				sleep($sleep_sec);
			};

		}
			
		function getInfoAboutArtist($id)
		{
			$api_query = "http://developer.echonest.com/api/v4/artist/profile?api_key=" . $this->api_key . '&bucket=genre&bucket=terms&bucket=hotttnesss&bucket=urls&bucket=hotttnesss_rank&bucket=artist_location&bucket=images';
			
			echo $api_query . PHP_EOL;
			$content = file_get_contents($api_query, true);
			$this->check_limits();

			$result = json_decode($content);							
			
			if (isset($result->response->artist))
				return $result->response->artist;
			else
				return null;	
		}
				
}



if (isset($_GET['method']))
{
	$variable = '';
	$variable2 = '';	
	$obj = new EchoNestApi();
	$method = $_GET['method'];
	
	if (isset($_GET['term']))
		$variable .= $_GET['term'];
	
	if (isset($_GET['genre']))
		$variable .= $_GET['genre'];
		
	if (isset($_GET['artist']))
		$variable .= $_GET['artist'];
	
	if (isset($_GET['title']))
		$variable2 = $_GET['title'];		
	
	
	//echo $variable; exit;
	
		//echo $_GET['genre']; exit;
		
	if ($variable2!='')
		$obj->$method($variable, $variable2);
	else
		$obj->$method($variable);

	
	
}	

//$obj = new EchoNestApi();

// $obj->GetSimilarArtist('ARH6W4X1187B99274F');


//$obj->GetArtistByGenre('grunge');

//$obj->GetSongByArtist('ARH3S5S1187FB4F76B');

//$obj->GetAllGenre();

//$obj->getListTerms('style');
//$obj->GetAlbumByArtist('nirvana', 'school');
			
	//$obj->GetSongByArtist('ARH3S5S1187FB4F76B');		
			
			
				// echo "<pre>";
				// print_r($obj->GetAllGenre());
				// echo "</pre>";
	
	
	
	
	
	// like sur artiste name
	//http://developer.echonest.com/api/v4/artist/suggest?api_key=SBPKL3LQ04MM2QHKW&name=ra&results=15			
	
	
	// like song sur deezer : http://api.deezer.com/search/autocomplete?q=smells
	
			
		//	$big_query = 'http://developer.echonest.com/api/v4/artist/profile?api_key=N6E4NIOVYMTHNDM8J&id=ARH6W4X1187B99274F&format=json&bucket=audio&bucket=biographies&bucket=blogs&bucket=familiarity&bucket=hotttnesss&bucket=images&bucket=news&bucket=reviews&bucket=terms&bucket=urls&bucket=video&bucket=id:musicbrainz';


//$conn = new ConnectLocalDB;
//$conn->ConnectDB();





//echo $conn->conn;
			
			
/*
$obj = new EchoNestApi();
$term = 'style';
$obj->GetListTerms($term, $conn->conn);			
	*/		
			
			
			
			
			
?>
