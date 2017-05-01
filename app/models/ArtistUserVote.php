<?php




class ArtistUserVote extends \Phalcon\Mvc\Model
{
	use Error;
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
    public $artist_id;
     
    /**
     *
     * @var string
     */
    public $artist_name;
     
    /**
     *
     * @var string
     */
    public $vote_date;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('Artist_User_Vote');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'artist_id' => 'artist_id', 
            'artist_name' => 'artist_name', 
            'vote_date' => 'vote_date'
        );
    }

}
