<?php




class UserSession extends \Phalcon\Mvc\Model
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
     * @var string
     */
    public $start_time;
     
    /**
     *
     * @var string
     */
    public $end_time;
     
    /**
     *
     * @var string
     */
    public $latitude;
     
    /**
     *
     * @var string
     */
    public $longitude;
     
    /**
     *
     * @var string
     */
    public $client_session_key;
     
    /**
     *
     * @var string
     */
    public $session_key;
     
    /**
     *
     * @var string
     */
    public $session_expires;
     
    /**
     *
     * @var string
     */
    public $ip;
     
    /**
     *
     * @var string
     */
    public $remember_me;
     
    /**
     *
     * @var string
     */
    public $device_type;
     
    /**
     *
     * @var integer
     */
    public $language_id;
     
    /**
     *
     * @var string
     */
    public $fb_token;
     
    /**
     *
     * @var string
     */
    public $fb_expires_at;
     
    /**
     *
     * @var string
     */
    public $deezer_token;
     
    /**
     *
     * @var string
     */
    public $deezer_expires_at;
    
    public function initialize()
    {
		$this->setSource('User_Session');

		//$this->skipAttributesonUpdate(array('user_id', 'start_time', 'ip'));	
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'start_time' => 'start_time', 
            'end_time' => 'end_time', 
            'latitude' => 'latitude', 
            'longitude' => 'longitude', 
            'client_session_key' => 'client_session_key', 
            'session_key' => 'session_key', 
            'session_expires' => 'session_expires', 
            'ip' => 'ip', 
            'remember_me' => 'remember_me', 
            'device_type' => 'device_type', 
            'language_id' => 'language_id', 
            'fb_token' => 'fb_token', 
            'fb_expires_at' => 'fb_expires_at', 
            'deezer_token' => 'deezer_token', 
            'deezer_expires_at' => 'deezer_expires_at'
        );
    }

}
