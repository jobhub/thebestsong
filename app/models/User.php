<?php

use Phalcon\Mvc\Model\Validator\Email as Email;
use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use TBS\Models\EmailConfirmations as EmailConfirmations;

class User extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $birthdate;

    /**
     *
     * @var string
     */
    public $registration_date;

    /**
     *
     * @var string
     */
    public $gender;

    /**
     *
     * @var string
     */
    public $country;

    /**
     *
     * @var string
     */
    public $city;

    /**
     *
     * @var string
     */
    public $is_facebook_connect;

    /**
     *
     * @var string
     */
    public $facebook_id;

    /**
     *
     * @var string
     */
    public $spotify_connect_token;

    /**
     *
     * @var string
     */
    public $deezer_connect_token;

    /**
     *
     * @var string
     */
    public $itunes_connect_token;

    /**
     *
     * @var integer
     */
    public $profile_id;

    /**
     *
     * @var integer
     */
    public $profile_coeff;

    /**
     *
     * @var integer
     */
    public $followers_number;

    /**
     *
     * @var integer
     */
    public $credibility_mentor_coeff;

    /**
     *
     * @var string
     */
    public $active;

    /**
     *
     * @var string
     */
    public $banned;

    /**
     *
     * @var string
     */
    public $suspended;

    public function validation() {
        $this->validate(
                new Email(
                array(
            "field" => "email",
            "required" => true,
        )));

        // Validate that emails are unique across users

        $this->validate(new Uniqueness(array(
            "field" => "email",
            "message" => "The email is already registered"
        )));

        return $this->validationHasFailed() != true;
    }

    public function afterSave() {
        $view_disabled = $this->getDI()->getView()->isDisabled();
        $logger = $this->getDI()->get("logger");

        if ($this->active == 'N') {
            $emailConfirmation = new EmailConfirmations();

            $emailConfirmation->usersId = $this->id;

            if ($emailConfirmation->save()) {
                if (!$view_disabled)
                    $this->getDI()
                            ->getFlash()
                            ->notice('A confirmation mail has been sent to ' . $this->email);
            } else {
                $logger->error("Could not save email confirmation ");
            }
        }
    }

    public function initialize() {
        $this->setSource('User');

        $this->skipAttributesonCreate(array("profile_coeff", "followers_number", "credibility_mentor_coeff", "banned", "suspended"));

        $this->hasOne('profile_id', 'UserProfile', 'id', array(
            'alias' => 'profile',
            'reusable' => true
        ));

        $this->auth = Phalcon\DI::getDefault()->get('auth');
        $this->logger 	= Phalcon\DI::getDefault()->get('logger');
    }

    public function updateCredibilityStyle($styles) {

        $user_id = $this->id;

        foreach ($styles as $style_id => $style_name) {
            // find entry for this style in User_Style_Credibility
            $entry 	= UserStyleCredibility::findFirst("user_id = $user_id and style_id = $style_id");
            $row 	= MusicalStyle::findFirst(array("id  = $style_id", "columns" => "votes_number"));
            $total_votes_style = $row->votes_number;

            // if found		
            if ($entry) {
                // inc votes
                $votes_number = ++$entry->votes_number;
                // calculate new coeff
                $new_coeff = $votes_number / $total_votes_style;
                $entry->previous_coeff = $entry->credibility_coeff;
                $entry->credibility_coeff = $new_coeff;
                
                if (!$entry->save())
					$this->logger->error($entry->getMessages()[0]);
            } else {
                // new entry
                $new_coeff = ($total_votes_style ? 1 / $total_votes_style : 1);

                $usc = new UserStyleCredibility();

                $usc->assign(array("user_id" => $user_id,
                    "style_id" => $style_id,
                    "style_name" => $style_name,
                    "credibility_coeff" => $new_coeff,
                    "previous_coeff" => 0,
                    "votes_number" => 1,
                    "last_update" => new Phalcon\Db\RawValue("NOW()")));

                if (!$usc->create()) 
					$this->logger->error($usc->getMessages()[0]);
            }
        }
    }

    public function getCredibilityCoeff($style_id) {
        $auth = Phalcon\DI::getDefault()->get('auth');
        $user_id = $auth->getId();

        $res = UserStyleCredibility::findFirst("user_id = $user_id AND style_id = $style_id");

        if ($res)
            return $res->credibility_coeff;
        else
            return 0;
    }

    public function register($name, $email, $password) {
        $result = array();
        $security = Phalcon\DI::getDefault()->get('security');

        $this->email = $email;
        $this->password = $security->hash($password);
        $this->name = $name;

        $this->registration_date = new Phalcon\Db\RawValue('now()');
        $this->active = 'N';
        // TBD Get Regular profile
        $profile = UserProfile::findByProfileName("regular");

        $this->profile_id = 1;
        $this->profile_coeff = 0.1;

        if ($this->create() == false) {
            $result["success"] = 0;

            foreach ($this->getMessages() as $message) {
                @$result["message"] .= $message . PHP_EOL;
            }
        } else {
            $result["success"] = 1;
            $result["message"] = 'Thanks for sign-up, please log-in';
        }

        return $result;
    }

    public function importFacebookMusicActivity($music) {
        $logger = $this->getDI()->get("logger");
        $db 	= $this->getDI()->getShared("db");

        $logger->log("First, delete what exists ...");
        $db->execute("DELETE FROM User_Fb_Musical_Activity 
					 WHERE user_id = " . $this->id);

        $logger->log("Inserting your Facebook music likes into User_Fb_Musical_Activity");


        $values = "";
        $sql = "INSERT IGNORE INTO User_Fb_Musical_Activity(user_id, artist_id, artist_fb_id, artist_name) VALUES ";

        if (!isset($music['data'])) {
            $logger->log("No data found in Fb Music ");

            return false;
        }

        foreach ($music['data'] as $artist) {
            $db_artist = Artist::findFirstByFacebookId($artist->id);

            if (!$db_artist)
                $db_artist = Artist::findFirstByName($artist->name);

            $artist_fb_id = $artist->id;
            $artist_id = ($db_artist ? $db_artist->id : 0);

            $values .= '(' . $this->id . ',' . $artist_id . ',' . $artist_fb_id . ',"' . $artist->name . '"),';
        }

        $query = rtrim($sql . $values, ',');

        $logger->log($query);

        $res = $db->execute($query);

        $logger->log($db->affectedRows() . " rows inserted in User_Fb_Musical_Activity");
    }

    public function importDeezerArtists($artists) {
        $logger = $this->getDI()->get("logger");
        $db = $this->getDI()->getShared("db");
        $values = array();

        $logger->log(print_r($artists, true));
        $logger->log("DELETE your Deezer Artists ");

        $db->execute("DELETE FROM User_Deezer_Artist 
					 WHERE user_id = " . $this->id);

        $logger->log("Importing your Deezer artists into User_Deezer_Artist ");

        $sql = "INSERT IGNORE INTO User_Deezer_Artist(user_id, artist_id, artist_name, deezer_artist_id, insert_date) VALUES ";
        $values_clause = "(?, ?, ?, ?, NOW()),";

        foreach ($artists as $deezer_id => $artist_name) {
            $sql .= $values_clause;

            $query = "SELECT id FROM Artist WHERE deezer_id = $deezer_id";

            $res = $db->fetchOne($query, Phalcon\Db::FETCH_OBJ);

            if ($res)
                $artist_id = $res->id;
            else
                $artist_id = 0;

            array_push($values, $this->id, $artist_id, $artist_name, $deezer_id);
        }

        $db->execute(rtrim($sql, ','), $values);

        $logger->log($db->affectedRows() . " rows inserted in User_Deezer_Artist");
    }

    // import artists from Deezer playlists 	
    public function importDeezerPlaylistArtists($artist) {
        
    }

    public function getBestSongs() {
        $db = $this->getDI()->getShared("db");
        
        $sql = "SELECT S.id, S.song_name, S.artist_id, S.artist_name, S.album_id, S.album_name, S.votes_number, S.general_score, S.hotttnesss, A.album_visual
				FROM Song S, User_Liked_Songs U, Album A 
				WHERE U.user_id = " . $this->id . 
				" AND S.id = U.song_id 
				AND S.album_id = A.id 
				ORDER BY U.id DESC
				LIMIT 30 ";
				
		return $db->fetchAll($sql, Phalcon\Db::FETCH_OBJ);				
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap() {
        return array(
            'id' => 'id',
            'email' => 'email',
            'password' => 'password',
            'name' => 'name',
            'birthdate' => 'birthdate',
            'registration_date' => 'registration_date',
            'gender' => 'gender',
            'country' => 'country',
            'city' => 'city',
            'is_facebook_connect' => 'is_facebook_connect',
            'facebook_id' => 'facebook_id',
            'spotify_connect_token' => 'spotify_connect_token',
            'deezer_connect_token' => 'deezer_connect_token',
            'itunes_connect_token' => 'itunes_connect_token',
            'profile_id' => 'profile_id',
            'profile_coeff' => 'profile_coeff',
            'followers_number' => 'followers_number',
            'credibility_mentor_coeff' => 'credibility_mentor_coeff',
            'active' => 'active',
            'banned' => 'banned',
            'suspended' => 'suspended'
        );
    }

}
