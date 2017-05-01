<?php

class VoteController extends ControllerBase {
    /* function VoteSongAction 
     * @param song_id
     * @param vote up|down
     * 
     * @return bool true if success
     */

    public function VoteSongAction() {
        $song_id = $this->request->getPost('song_id');        
        $vote = $this->request->getPost('vote');
        $client_session_key = $this->request->getPost("client_session_key");
        $session_key = $this->request->getPost("session_key");
        $user_id = $this->request->getPost("user_id");

        if ($vote == "-1")
            return $this->dispatcher->forward(array(
                        "action" => "DislikeSong"
            ));

        $this->setJsonResponse();
       
        if (!$this->checkSession())
            return array("success" => 0);

        $result = array();


        try {
            $song_vote = SongUserVote::findFirst(array("song_id = :song_id: AND user_id = :user_id: ", "bind" =>
                        array("song_id" => $song_id, "user_id" => $user_id)));
            
            // if the user did not vote on this song 
            if (!$song_vote) {
                // start transaction 
                $this->db->begin();

                // get album/artist info
                $song = Song::findFirstById($song_id);

                // save to song_user_vote
                $song_vote = new SongUserVote();
                $song_vote->assign(array("user_id" => $user_id,
                    "song_id" => $song_id,
                    "song_name" => $song->song_name,
                    "album_id" => $song->album_id,
                    "album_name" => $song->album_name,
                    "artist_id" => $song->artist_id,
                    "artist_name" => $song->artist_name,
                    "vote_date" => new Phalcon\Db\RawValue('now()')
                ));

                if (!$song_vote->save())
                    $song_vote->error();

                // save to album_user_vote
                if (!is_null($song->album_id) && !AlbumUserVote::findFirst("album_id = " . $song->album_id . "  AND user_id = " . $user_id)) {
                    $album_vote = new AlbumUserVote();

                    $album_vote->assign(array(
                        "user_id" => $user_id,
                        "album_id" => $song->album_id,
                        "album_name" => $song->album_name,
                        "vote_date" => new Phalcon\Db\RawValue('now()')));

                    if (!$album_vote->save())
                        $album_vote->error();
                }

                // save to artist_user_vote : allow multiple votes for artist ?
                $artist_vote = new ArtistUserVote();

                $artist_vote->assign(array(
                    "user_id" => $user_id,
                    "artist_id" => $song->artist_id,
                    "artist_name" => $song->artist_name,
                    "vote_date" => new Phalcon\Db\RawValue('now()')
                ));

                if (!$artist_vote->create())
                    $artist_vote->error();

                // Push song into user_liked_songs table 
                $item = new UserLikedSongs();

                if (!$item->save(array(
                            "user_id" => $user_id,
                            "song_id" => $song_id,
                            "liked_date" => new Phalcon\Db\RawValue("now()"))))
                    $item->error();

                // save song into current playlist 
                if ($current_playlist = UserPlaylist::getCurrentPlaylist($user_id))
                    $current_playlist->saveSong($song_id);

                // Update score of the song
                $song->updateScores();

                // Update score of the album
                if (!is_null($song->album_id)){
                    $song->album->updateScores();
                }

                // Update score of the artist
                if (!empty($song->artist))
                    if (!$song->artist->updateScores())
						echo "ERROR " . $song->artist->getMessages()[0] . PHP_EOL;

                // update credibility_style_coeff 	
                $styles = $song->getStyles();

                $user = User::findFirstById($user_id);
                $user->updateCredibilityStyle($styles);

                // increment votes_number in musical_style
                foreach ($styles as $id => $style) {
                    $mstyle = MusicalStyle::findFirstById($id);
                    $mstyle->incrementVotes();
                }

                $this->db->commit();
            } else {
                $result["message"] = "You already voted for this song!";
            }
        } catch (Exception $e) {
            if ($this->db->isUnderTransaction())
                $this->db->rollback();

            return $e->getMessage();
        }

        if (!isset($result["message"]))
            $result["success"] = 1;
        else
            $result["success"] = 0;

        return $result;
    }

    // push song into user_liked_songs table 
    public function LikeSongAction($song_id) {
        if (!$user = $this->auth->getUser())
            return false;

        // search song 
        if (!$song = Song::FindById($song_id))
            return false;

        $item = new User_Liked_Songs();

        $ret = $item->save(array("user_id" => $user->id, "song_id" => $song_id, "liked_date" => new Phalcon\Db\RawValue("now()")));

        return $ret;
    }

    public function DislikeSongAction() {
        $this->setJSONResponse();

        if (!$this->checkSession())
            return array("success" => 0);

        if (!$user = $this->auth->getUser())
            return array("success" => 0);

        $user_id = $this->request->getPost("user_id");
        $song_id = $this->request->getPost("song_id");

        // search song 
        if (!$song = Song::findFirstById($song_id))
            return array("success" => 0);

        $item = new UserDislikedSongs();

        $ret = $item->save(array(
            "user_id" => $user_id,
            "song_id" => $song_id,
            "disliked_date" => new Phalcon\Db\RawValue('now()')));

        if ($ret)
            return array("success" => 1);
        else
            return array("success" => 0, "message" => $item->getMessages()[0]);
    }

}
