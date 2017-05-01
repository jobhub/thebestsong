<?php




class SongUserVote extends \Phalcon\Mvc\Model
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
     * @var integer
     */
    public $song_id;
     
    /**
     *
     * @var string
     */
    public $song_name;
     
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
     * @var string
     */
    public $vote_date;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('Song_User_Vote');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'song_id' => 'song_id', 
            'song_name' => 'song_name', 
            'album_id' => 'album_id', 
            'album_name' => 'album_name', 
            'artist_id' => 'artist_id', 
            'artist_name' => 'artist_name', 
            'vote_date' => 'vote_date'
        );
    }

}
