<?php

class UserSuggestedSongs extends \Phalcon\Mvc\Model
{

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
    public $suggested_time;

    /**
     *
     * @var string
     */
    public $session_key;

    /**
     *
     * @var string
     */
    public $status;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('User_Suggested_Songs');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'user_id' => 'user_id', 
            'song_id' => 'song_id', 
            'suggested_time' => 'suggested_time', 
            'session_key' => 'session_key', 
            'status' => 'status'
        );
    }

}
