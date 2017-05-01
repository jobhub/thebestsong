<?php




class AlbumBestSong extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $album_best_song_id;
     
    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var string
     */
    public $song_name;
     
    /**
     *
     * @var double
     */
    public $general_score;
     
    /**
     *
     * @var integer
     */
    public $album_id;
     
    /**
     *
     * @var string
     */
    public $album_name;
     
    /**
     *
     * @var integer
     */
    public $artist_id;
     
    /**
     *
     * @var string
     */
    public $artist_name;
     
    /**
     *
     * @var double
     */
    public $hotttnesss;
     
    /**
     *
     * @var string
     */
    public $style1_name;
     
    /**
     *
     * @var integer
     */
    public $style1_id;
     
    /**
     *
     * @var string
     */
    public $style2_name;
     
    /**
     *
     * @var integer
     */
    public $style2_id;
     
    /**
     *
     * @var string
     */
    public $style3_name;
     
    /**
     *
     * @var integer
     */
    public $style3_id;
     
    /**
     *
     * @var string
     */
    public $deezer_url;
     
    /**
     *
     * @var string
     */
    public $spotify_url;
     
    /**
     *
     * @var integer
     */
    public $deezer_track_id;
     
    /**
     *
     * @var string
     */
    public $spotify_album_id;
     
    /**
     *
     * @var string
     */
    public $itunes_url;
     
    /**
     *
     * @var string
     */
    public $itunes_previewurl;
     
    /**
     *
     * @var string
     */
    public $youtube_url;
     
    /**
     *
     * @var string
     */
    public $deezer_preview_url;
     
    /**
     *
     * @var string
     */
    public $album_date;
     
    /**
     *
     * @var integer
     */
    public $votes_number;
     
    /**
     *
     * @var string
     */
    public $album_visual;
    
    public function initialize()
    {
		$this->setSource('Album_Best_Song');

    }


    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'album_best_song_id' => 'album_best_song_id', 
            'id' => 'id', 
            'song_name' => 'song_name', 
            'general_score' => 'general_score', 
            'album_id' => 'album_id', 
            'album_name' => 'album_name', 
            'artist_id' => 'artist_id', 
            'artist_name' => 'artist_name', 
            'hotttnesss' => 'hotttnesss', 
            'style1_name' => 'style1_name', 
            'style1_id' => 'style1_id', 
            'style2_name' => 'style2_name', 
            'style2_id' => 'style2_id', 
            'style3_name' => 'style3_name', 
            'style3_id' => 'style3_id', 
            'deezer_url' => 'deezer_url', 
            'spotify_url' => 'spotify_url', 
            'deezer_track_id' => 'deezer_track_id', 
            'spotify_album_id' => 'spotify_album_id', 
            'itunes_url' => 'itunes_url', 
            'itunes_previewurl' => 'itunes_previewurl', 
            'youtube_url' => 'youtube_url', 
            'deezer_preview_url' => 'deezer_preview_url', 
            'album_date' => 'album_date', 
            'votes_number' => 'votes_number', 
            'album_visual' => 'album_visual'
        );
    }

}
