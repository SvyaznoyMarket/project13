<?php

namespace EnterSite\Helper;

class Translate {
    /**
     * @param int $number  Например: 1, 43, 112
     * @param array $choices Например: ['товар', 'товара', 'товаров']
     * @return mixed
     */
    public function numberChoice($number, array $choices) {
        $cases = [2, 0, 1, 1, 1, 2];

        return $choices[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * @param \DateTime $date
     * @return string
     */
    public function humanizeDate(\DateTime $date) {
        $today = new \DateTime();
        $today->settime(0, 0, 0);

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

        return 'через ' . ($interval->days - 1) . ' ' . $this->numberChoice(($interval->days - 1), ['день', 'дня', 'дней']) . ' (' . $date->format('d.m.Y') . ')';
    }
}