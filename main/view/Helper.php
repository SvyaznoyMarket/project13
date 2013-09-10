<?php

namespace View;

class Helper extends \Templating\Helper {
    /**
     * @param $value
     * @return int|string
     */
    public function clearZeroValue($value) {
        $frac = $value - floor($value);
        if (0 == $frac) {
            return intval($value);
        } else {
            return rtrim($value, '0');
        }
    }

    /**
     * @param $date
     * @return string
     */
    public function humanizeDate($date) {
        $today = new \DateTime();
        $today->settime(0, 0, 0);
        if (!is_object($date) || !is_a($date, 'DateTime')) {
            $date = new \DateTime($date);
        }

        $interval = $today->diff($date);
        if ($interval->days == 0) {
            return 'Сегодня, ' . $date->format('d.m.Y');
        }
        if ($interval->days == 1 && $interval->invert == 0) { //если invert = 1 - значит дата уже прошла
            return 'Завтра, ' . $date->format('d.m.Y');
        }
        if ($interval->days == 2 && $interval->invert == 0) { //если invert = 1 - значит дата уже прошла
            return 'Послезавтра, ' . $date->format('d.m.Y');
        }

        return 'через ' . ($interval->days - 1) . ' ' . $this->numberChoice(($interval->days - 1), array('день', 'дня', 'дней')) . ' (' . $date->format('d.m.Y') . ')';
    }

    public function getDaysDiff($date) {
        $today = new \DateTime();
        $today->settime(0, 0, 0);
        if (!is_object($date) || !is_a($date, 'DateTime')) {
            $date = new \DateTime($date);
        }
        $interval = $today->diff($date);
        return ($interval->days - 1 < 0) ? 0 : ($interval->days - 1);
    }


    /**
     * Укругляет до 0.1 с избытком:
     * либо отбрасыванием дробной части, либо отбрасыванием и прибавлением 0.1
     * Используется, например, для задания верхней и нижней границы фильтров.
     *
     * @param $value
     * @param bool $increase
     * @return bool|float
     */
    public function roundToOneDecimal($value, $increase = false) {

        $old_value = $value;
        if ( !is_numeric($value) ) return false;
        $value = (int) ( $value * 10 );
        if ( $increase && (10*$old_value - $value)> 1 ) $value++;
        $ret = ( ($value) ) / 10;

        return $ret;
    }

}
