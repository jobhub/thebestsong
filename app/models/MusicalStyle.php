<?php




class MusicalStyle extends \Phalcon\Mvc\Model
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
    public $style_name;
     
    /**
     *
     * @var integer
     */
    public $parent_id;
     
    /**
     *
     * @var integer
     */
    public $label_id;
     
    /**
     *
     * @var integer
     */
    public $level;
     
    /**
     *
     * @var string
     */
    public $description;
     
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
		$this->setSource('Musical_Style');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'style_name' => 'style_name', 
            'parent_id' => 'parent_id', 
            'label_id' => 'label_id', 
            'level' => 'level', 
            'description' => 'description', 
            'votes_number' => 'votes_number'
        );
    }
    
    public function incrementVotes()
    {
		$this->votes_number++;
		return $this->save();		
	}

}
