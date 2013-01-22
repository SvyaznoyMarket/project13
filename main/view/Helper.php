<?php

namespace View;

class Helper extends \Templating\Helper {
    /**
     * @param $price
     * @return string
     */
    public function formatPrice($price) {
        return number_format($price, 0, ',', ' ');
    }

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
            return 'сегодня (' . $date->format('d.m.Y') . ')';
        }
        if ($interval->days == 1 && $interval->invert == 0) { //если invert = 1 - значит дата уже прошла
            return 'завтра (' . $date->format('d.m.Y') . ')';
        }
        if ($interval->days == 2 && $interval->invert == 0) { //если invert = 1 - значит дата уже прошла
            return 'послезавтра (' . $date->format('d.m.Y') . ')';
        }

        return 'через ' . ($interval->days - 1) . ' ' . $this->numberChoice(($interval->days - 1), array('день', 'дня', 'дней')) . ' (' . $date->format('d.m.Y') . ')';
    }
}
