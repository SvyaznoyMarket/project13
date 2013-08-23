<?php

namespace Util;

class Geo {
    /**
     * Возвращает дистанцию в км
     *
     * @param $latitude1
     * @param $longitude1
     * @param $latitude2
     * @param $longitude2
     * @return float
     */
    public static function distance($latitude1, $longitude1, $latitude2, $longitude2) {
        $pi80 = M_PI / 180;
        $latitude1 *= $pi80;
        $longitude1 *= $pi80;
        $latitude2 *= $pi80;
        $longitude2 *= $pi80;

        $r = 6372.797; // км
        $latitudeDistance = $latitude2 - $latitude1;
        $longitudeDistance = $longitude2 - $longitude1;
        $a = sin($latitudeDistance / 2) * sin($latitudeDistance / 2) + cos($latitude1) * cos($latitude2) * sin($longitudeDistance / 2) * sin($longitudeDistance / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $result = $r * $c;

        return $result;
    }
}