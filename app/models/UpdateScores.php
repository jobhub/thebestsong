<?php

trait UpdateScores {

    public function updateScores() {
        $auth = Phalcon\DI::getDefault()->get('auth');
        $user = $auth->getUser();

        if (!$user)
            throw new Exception("You are  not authorized to do this");

        $total = 0;

        // update style_scores
        for ($i = 1; $i <= 10; $i++) {
            if (!empty($this->{"style" . $i . "_id"})) {
                $weight = $this->{"style" . $i . "_weight"};
                $cuc = $user->getCredibilityCoeff($this->{"style" . $i . "_id"});
                $cmc = $user->credibility_mentor_coeff;
                $pc = $user->profile_coeff;
                $ps = $this->{"style" . $i . "_score_weighted"};

                $new_score = 1 + ($weight * ($cuc + $cmc) * $pc) + $ps;
                $this->{"style" . $i . "_score_weighted"} = $new_score;

                $total += $new_score;
            }
        }

        $this->general_score = $total / 10;

        // increment votes_number
        $this->votes_number++;

        return $this->save();
    }

}
