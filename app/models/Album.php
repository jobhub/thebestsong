<?php

class Album extends \Phalcon\Mvc\Model {

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
    public $album_name;

    /**
     *
     * @var string
     */
    public $album_date;

    /**
     *
     * @var integer
     */
    public $album_year;

    /**
     *
     * @var string
     */
    public $description;

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
     * @var integer
     */
    public $general_score;

    /**
     *
     * @var string
     */
    public $album_visual;

    /**
     *
     * @var string
     */
    public $deezer_uri;

    /**
     *
     * @var string
     */
    public $spotify_uri;

    /**
     *
     * @var string
     */
    public $youtube_uri;

    /**
     *
     * @var string
     */
    public $itunes_uri;

    /**
     *
     * @var string
     */
    public $mood;

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
    public $musicbrainz_id;
    
    /**
     *
     * @var string
     */
    public $deezer_track_list;
    
    /**
     *
     * @var int
     */
    public $is_song_completed;
    
    /**
     *
     * @var int
     */
    public $deezer_id;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSource('Album');

        $this->hasMany("id", "Song", "album_id", array("alias" => "songs"));
    }

    public function error() {
        echo "ERROR: Could not save album id : " . $this->id . ", name : " . $this->album_name . PHP_EOL;

        foreach ($this->getMessages() as $message) {
            echo $message . PHP_EOL;
        }
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap() {
        return array(
            'id' => 'id',
            'album_name' => 'album_name',
            'album_date' => 'album_date',
            'album_year' => 'album_year',
            'description' => 'description',
            'artist_id' => 'artist_id',
            'artist_name' => 'artist_name',
            'country' => 'country',
            'city' => 'city',
            'votes_number' => 'votes_number',
            'general_score' => 'general_score',
            'album_visual' => 'album_visual',
            'deezer_uri' => 'deezer_uri',
            'spotify_uri' => 'spotify_uri',
            'youtube_uri' => 'youtube_uri',
            'itunes_uri' => 'itunes_uri',
            'mood' => 'mood',
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
            'musicbrainz_id' => 'musicbrainz_id',
            'deezer_track_list' => 'deezer_track_list',
            'is_song_completed' => 'is_song_completed',
            'deezer_id' => 'deezer_id'
        );
    }

}
