<?php




class AlbumUserVote extends \Phalcon\Mvc\Model
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
    public $vote_date;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('Album_User_Vote');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'album_id' => 'album_id', 
            'album_name' => 'album_name', 
            'vote_date' => 'vote_date'
        );
    }

}
