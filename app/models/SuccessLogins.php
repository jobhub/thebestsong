<?php

namespace TBS\Models;

class SuccessLogins extends \Phalcon\Mvc\Model
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
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'usersId' => 'usersId', 
            'ipAddress' => 'ipAddress', 
            'userAgent' => 'userAgent'
        );
    }

}
