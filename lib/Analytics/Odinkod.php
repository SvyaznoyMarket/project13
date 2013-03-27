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
        // Белгородская область
        } else if (73 == $region->getParentId()) {
            $return = '19549-9fbf23';
        // Брянская область
        } else if (74 == $region->getParentId()) {
            $return = '19550-fc36b3';
        // Краснодарский край
        } else if (96 == $region->getParentId()) {
            $return = '19551-65d5a6';
        // Липецкая область
        } else if (81 == $region->getParentId()) {
            $return = '19552-13e8a3';
        // Нижегородская область
        } else if (18 == $region->getParentId()) {
            $return = '19548-1be4a1';
        // Орловская область
        } else if (84 == $region->getParentId()) {
            $return = '19553-a6b576';
        // Тульская область
        } else if (89 == $region->getParentId()) {
            $return = '19554-f24802';
        // Тверская область
        } else if (88 == $region->getParentId()) {
            $return = '19555-b85944';
        // Ярославская область
        } else if (90 == $region->getParentId()) {
            $return = '19556-61f229';
        }


        return $return;
    }
}