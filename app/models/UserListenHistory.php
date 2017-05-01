<?php




class UserListenHistory extends \Phalcon\Mvc\Model
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
    public $listen_date;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('User_Listen_History');
		
		$this->skipAttributesOnCreate(array("listen_date"));

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
            'listen_date' => 'listen_date'
        );
    }

}
