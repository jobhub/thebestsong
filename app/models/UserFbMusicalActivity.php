<?php

class UserFbMusicalActivity extends \Phalcon\Mvc\Model {

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
     * @var integer
     */
    public $artist_fb_id;

    /**
     *
     * @var string
     */
    public $artist_name;

    /**
     *
     * @var string
     */
    public $created;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSource('User_Fb_Musical_Activity');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap() {
        return array(
            'id' => 'id',
            'user_id' => 'user_id',
            'artist_id' => 'artist_id',
            'artist_fb_id' => 'artist_fb_id',
            'artist_name' => 'artist_name',
            'created' => 'created'
        );
    }

}
