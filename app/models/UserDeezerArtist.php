<?php

class UserDeezerArtist extends \Phalcon\Mvc\Model
{

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
    public $deezer_artist_id;

    /**
     *
     * @var string
     */
    public $insert_date;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('User_Deezer_Artist');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'artist_id' => 'artist_id', 
            'artist_name' => 'artist_name', 
            'deezer_artist_id' => 'deezer_artist_id', 
            'insert_date' => 'insert_date'
        );
    }

}
