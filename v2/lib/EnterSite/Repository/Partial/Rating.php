<?php

namespace EnterSite\Repository\Partial;

use EnterSite\Model\Partial;

class Rating {
    /**
     * @param $score
     * @return Partial\Rating\Star[]
     */
    public function getStarList(
        $score
    ) {
        $stars = [];

        for ($i = 0; $i < (int)$score; $i++) {
            $star = new Partial\Rating\Star();
            $star->image = 'star.png';
            $stars[] = $star;
        }
        if (ceil($score) > $score) {
            $star = new Partial\Rating\Star();
            $star->image = 'starHalf.png';
            $stars[] = $star;
        }
        for ($i = 5; $i > ceil($score); $i--) {
            $star = new Partial\Rating\Star();
            $star->image = 'starEmpty.png';
            $stars[] = $star;
        }

        return $stars;
    }
}