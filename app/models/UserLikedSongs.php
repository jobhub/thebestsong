<?php




class UserLikedSongs extends \Phalcon\Mvc\Model
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
    public $liked_date;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('User_Liked_Songs');

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
            'liked_date' => 'liked_date'
        );
    }

}
