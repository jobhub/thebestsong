<?php




class PasswordChanges extends \Phalcon\Mvc\Model
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
    public $usersId;
     
    /**
     *
     * @var string
     */
    public $ipAddress;
     
    /**
     *
     * @var string
     */
    public $userAgent;
     
    /**
     *
     * @var integer
     */
    public $createdAt;

    /**
     * Before create 
     */
    public function beforeValidationOnCreate()
    {
        // Timestamp the confirmaton
        $this->createdAt = time();

        // Set status to non-confirmed
        $this->reset = 'N';
    }

    public function initialize()
    {
        $this->belongsTo('usersId', 'User', 'id', array(
            'alias' => 'user'
        ));
    }
         
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'usersId' => 'usersId', 
            'ipAddress' => 'ipAddress', 
            'userAgent' => 'userAgent', 
            'createdAt' => 'createdAt'
        );
    }

}
