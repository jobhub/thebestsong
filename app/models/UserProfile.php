<?php




class UserProfile extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var string
     */
    public $profile_name;
     
    /**
     *
     * @var double
     */
    public $profile_coefficient;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('User_Profile');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'profile_name' => 'profile_name', 
            'profile_coefficient' => 'profile_coefficient'
        );
    }

}
