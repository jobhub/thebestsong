<?php

namespace TBS\Models;

class FailedLogins extends \Phalcon\Mvc\Model
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
     * @var integer
     */
    public $attempted;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'usersId' => 'usersId', 
            'ipAddress' => 'ipAddress', 
            'attempted' => 'attempted'
        );
    }

}
