<?php

namespace Enter\Helper;

class Date {
    /**
     * @param \DateTime $date
     * @return string
     */
    public function dateToRu(\DateTime $date) {
        $monthsEnRu = [
            'January'   => 'января',
            'February'  => 'февраля',
            'March'     => 'марта',
            'April'     => 'апреля',
            'May'       => 'мая',
            'June'      => 'июня',
            'July'      => 'июля',
            'August'    => 'августа',
            'September' => 'сентября',
            'October'   => 'октября',
            'November'  => 'ноября',
            'December'  => 'декабря',
        ];
        $dateEn = $date->format('j F Y');
        $dateRu = $dateEn;
        foreach ($monthsEnRu as $monthsEn => $monthsRu) {
            if (preg_match("/$monthsEn/", $dateEn)) {
                $dateRu = preg_replace("/$monthsEn/", $monthsRu, $dateEn);
            }
        }

        return $dateRu;
    }
}