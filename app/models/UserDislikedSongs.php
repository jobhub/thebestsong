<?php




class UserDislikedSongs extends \Phalcon\Mvc\Model
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
    public $song_id;
     
    /**
     *
     * @var string
     */
    public $disliked_date;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('User_Disliked_Songs');

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
            'disliked_date' => 'disliked_date'
        );
    }

}
