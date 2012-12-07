<?php

namespace Analytics;

class Odinkod {
    public static function getCusId(\Model\Region\Entity $region) {
        $return = null;

        // Москва
        if (14974 == $region->getId()) {
            $return = '12675-ab10d5';
        // Санкт-Петербург
        } else if (108136 == $region->getId()) {
            $return = '16201-394f47';
        // Нижний Новгород
        } else if (99958 == $region->getId()) {
            $return = '16202-ec0cb1';
        // Воронежская область
        } else if (76 == $region->getParentId()) {
            $return = '17408-e29adc';
        }

        return $return;
    }
}