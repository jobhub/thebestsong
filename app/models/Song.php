<?php

class Song extends \Phalcon\Mvc\Model {

    use UpdateScores;

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $echonest_id;

    /**
     *
     * @var string
     */
    public $song_name;

    /**
     *
     * @var string
     */
    public $song_date;

    /**
     *
     * @var integer
     */
    public $song_year;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var double
     */
    public $duration;

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
     * @var string
     */
    public $other_album_ids;

    /**
     *
     * @var string
     */
    public $country;

    /**
     *
     * @var string
     */
    public $city;

    /**
     *
     * @var integer
     */
    public $votes_number;

    /**
     *
     * @var double
     */
    public $general_score;

    /**
     *
     * @var string
     */
    public $mood;

    /**
     *
     * @var double
     */
    public $hotttnesss;

    /**
     *
     * @var integer
     */
    public $style1_id;

    /**
     *
     * @var string
     */
    public $style1_name;

    /**
     *
     * @var double
     */
    public $style1_weight;

    /**
     *
     * @var double
     */
    public $style1_score_weighted;

    /**
     *
     * @var integer
     */
    public $style2_id;

    /**
     *
     * @var string
     */
    public $style2_name;

    /**
     *
     * @var double
     */
    public $style2_weight;

    /**
     *
     * @var double
     */
    public $style2_score_weighted;

    /**
     *
     * @var integer
     */
    public $style3_id;

    /**
     *
     * @var string
     */
    public $style3_name;

    /**
     *
     * @var double
     */
    public $style3_weight;

    /**
     *
     * @var double
     */
    public $style3_score_weighted;

    /**
     *
     * @var integer
     */
    public $style4_id;

    /**
     *
     * @var string
     */
    public $style4_name;

    /**
     *
     * @var double
     */
    public $style4_weight;

    /**
     *
     * @var double
     */
    public $style4_score_weighted;

    /**
     *
     * @var integer
     */
    public $style5_id;

    /**
     *
     * @var string
     */
    public $style5_name;

    /**
     *
     * @var double
     */
    public $style5_weight;

    /**
     *
     * @var double
     */
    public $style5_score_weighted;

    /**
     *
     * @var integer
     */
    public $style6_id;

    /**
     *
     * @var string
     */
    public $style6_name;

    /**
     *
     * @var double
     */
    public $style6_weight;

    /**
     *
     * @var double
     */
    public $style6_score_weighted;

    /**
     *
     * @var integer
     */
    public $style7_id;

    /**
     *
     * @var string
     */
    public $style7_name;

    /**
     *
     * @var double
     */
    public $style7_weight;

    /**
     *
     * @var double
     */
    public $style7_score_weighted;

    /**
     *
     * @var integer
     */
    public $style8_id;

    /**
     *
     * @var string
     */
    public $style8_name;

    /**
     *
     * @var double
     */
    public $style8_weight;

    /**
     *
     * @var double
     */
    public $style8_score_weighted;

    /**
     *
     * @var integer
     */
    public $style9_id;

    /**
     *
     * @var string
     */
    public $style9_name;

    /**
     *
     * @var double
     */
    public $style9_weight;

    /**
     *
     * @var double
     */
    public $style9_score_weighted;

    /**
     *
     * @var integer
     */
    public $style10_id;

    /**
     *
     * @var string
     */
    public $style10_name;

    /**
     *
     * @var double
     */
    public $style10_weight;

    /**
     *
     * @var double
     */
    public $style10_score_weighted;

    /**
     *
     * @var string
     */
    public $deezer_url;

    /**
     *
     * @var integer
     */
    public $deezer_track_id;

    /**
     *
     * @var string
     */
    public $spotify_url;

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
    public $track_position;

    public function initialize() {
        $this->setSource('Song');

        //Skips fields/columns on both INSERT/UPDATE operations
        //$this->skipAttributesonCreate(array('description', 'other_album_ids', 'mood', 'spotify_url', 'itunes_url',  'youtube_url'));	
        // relationship with Album 
        $this->belongsTo("album_id", "Album", "id");
        $this->hasOne("artist_id", "Artist", "id");
    }

    public function error() {
        echo "ERROR: Could not save song id : " . $this->id . ", name : " . $this->song_name . PHP_EOL;

        foreach ($this->getMessages() as $message) {
            echo $message . PHP_EOL;
        }
    }

    public function getStyles() {
        $styles = array();

        for ($i = 1; $i < 10; $i++) {
            if ($this->{"style" . $i . "_id"})
                $styles[$this->{"style" . $i . "_id"}] = $this->{"style" . $i . "_name"};
        }

        return $styles;
    }

    /*
     * Returns a random song with these styles 
     * 
     */

    public static function getSongWithStyles($style1_id, $style2_id, $style3_id) {
        // temporary : take into account only first 2 styles
        $sql = "SELECT S.*, A.*
				FROM Song S, Album A
				WHERE S.style1_id = $style1_id AND S.style2_id = $style2_id 
				AND S.deezer_preview_url IS NOT NULL 
				AND S.album_id = A.id 
				ORDER BY hotttnesss DESC, RAND()
				LIMIT 1";

        $db = Phalcon\DI::getDefault()->getShared("db");

        $result = $db->query($sql);
        //$result->setFetchMode(Phalcon\Db::FETCH_OBJ);

        return $result->fetchArray();
        //return $song;	
    }

    public static function getAlbumSongs($album_id) {
        $songs = self::find(array("album_id = $album_id", "columns" => "id, song_name, deezer_preview_url, deezer_track_id, style1_id, style2_id, style3_id", "order" => "track_position ASC"));

        return $songs->toArray();
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap() {
        return array(
            'id' => 'id',
            'echonest_id' => 'echonest_id',
            'song_name' => 'song_name',
            'song_date' => 'song_date',
            'song_year' => 'song_year',
            'description' => 'description',
            'duration' => 'duration',
            'artist_id' => 'artist_id',
            'artist_name' => 'artist_name',
            'album_id' => 'album_id',
            'album_name' => 'album_name',
            'other_album_ids' => 'other_album_ids',
            'country' => 'country',
            'city' => 'city',
            'votes_number' => 'votes_number',
            'general_score' => 'general_score',
            'mood' => 'mood',
            'hotttnesss' => 'hotttnesss',
            'style1_id' => 'style1_id',
            'style1_name' => 'style1_name',
            'style1_weight' => 'style1_weight',
            'style1_score_weighted' => 'style1_score_weighted',
            'style2_id' => 'style2_id',
            'style2_name' => 'style2_name',
            'style2_weight' => 'style2_weight',
            'style2_score_weighted' => 'style2_score_weighted',
            'style3_id' => 'style3_id',
            'style3_name' => 'style3_name',
            'style3_weight' => 'style3_weight',
            'style3_score_weighted' => 'style3_score_weighted',
            'style4_id' => 'style4_id',
            'style4_name' => 'style4_name',
            'style4_weight' => 'style4_weight',
            'style4_score_weighted' => 'style4_score_weighted',
            'style5_id' => 'style5_id',
            'style5_name' => 'style5_name',
            'style5_weight' => 'style5_weight',
            'style5_score_weighted' => 'style5_score_weighted',
            'style6_id' => 'style6_id',
            'style6_name' => 'style6_name',
            'style6_weight' => 'style6_weight',
            'style6_score_weighted' => 'style6_score_weighted',
            'style7_id' => 'style7_id',
            'style7_name' => 'style7_name',
            'style7_weight' => 'style7_weight',
            'style7_score_weighted' => 'style7_score_weighted',
            'style8_id' => 'style8_id',
            'style8_name' => 'style8_name',
            'style8_weight' => 'style8_weight',
            'style8_score_weighted' => 'style8_score_weighted',
            'style9_id' => 'style9_id',
            'style9_name' => 'style9_name',
            'style9_weight' => 'style9_weight',
            'style9_score_weighted' => 'style9_score_weighted',
            'style10_id' => 'style10_id',
            'style10_name' => 'style10_name',
            'style10_weight' => 'style10_weight',
            'style10_score_weighted' => 'style10_score_weighted',
            'deezer_url' => 'deezer_url',
            'deezer_track_id' => 'deezer_track_id',
            'spotify_url' => 'spotify_url',
            'spotify_album_id' => 'spotify_album_id',
            'itunes_url' => 'itunes_url',
            'itunes_previewurl' => 'itunes_previewurl',
            'youtube_url' => 'youtube_url',
            'deezer_preview_url' => 'deezer_preview_url',
            'track_position' => 'track_position'
        );
    }

}
