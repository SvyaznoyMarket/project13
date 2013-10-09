<?php

namespace View;

class Helper extends \Templating\Helper {
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
}
