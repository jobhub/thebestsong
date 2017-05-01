<?php




class ArtistSimilarity extends \Phalcon\Mvc\Model
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
    public $similar_artists_ids;
     
    /**
     *
     * @var string
     */
    public $similar_artist_name;
     
    /**
     *
     * @var integer
     */
    public $familiarity;
     
    /**
     *
     * @var integer
     */
    public $hotttness;
    
    public function initialize()
    {
		$this->setSource('Artist_Similarity');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'artist_id' => 'artist_id', 
            'artist_name' => 'artist_name', 
            'similar_artists_ids' => 'similar_artists_ids'
        );
    }

}
