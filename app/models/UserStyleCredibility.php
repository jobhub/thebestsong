<?php




class UserStyleCredibility extends \Phalcon\Mvc\Model
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
    public $style_id;
     
    /**
     *
     * @var string
     */
    public $style_name;
     
    /**
     *
     * @var double
     */
    public $credibility_coeff;
     
    /**
     *
     * @var double
     */
    public $previous_coeff;
     
    /**
     *
     * @var string
     */
    public $last_update;
     
    /**
     *
     * @var integer
     */
    public $votes_number;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('User_Style_Credibility');
		$this->skipAttributesOnUpdate(array("last_update"));
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'style_id' => 'style_id', 
            'style_name' => 'style_name', 
            'credibility_coeff' => 'credibility_coeff', 
            'previous_coeff' => 'previous_coeff', 
            'last_update' => 'last_update', 
            'votes_number' => 'votes_number'
        );
    }

}
