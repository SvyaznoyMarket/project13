<?php

namespace View;

class Helper {
    public function replacedUrl(array $replaces, array $excluded = null, $route = null) {
        $request = \App::request();

        if (null == $route) {
            if (!$request->attributes->has('route')) {
                throw new \RuntimeException('В атрибутах запроса не задан параметр "route".');
            }

            $route = $request->attributes->get('route');
        }

        $excluded = (null == $excluded) ? array('page' => '1') : $excluded;

        $params = array();
        foreach (array_diff(array_keys($request->attributes->all()), array('pattern', 'method', 'action', 'route', 'require')) as $k) {
            $params[$k] = $request->attributes->get($k);
        }
        foreach ($request->query->all() as $k => $v) {
            $params[$k] = $v;
        }
        foreach ($replaces as $k => $v) {
            if (null === $v) {
                if (isset($params[$k])) unset($params[$k]);
                continue;
            }

            $params[$k] = $v;
        }

        $params = array_diff_assoc($params, $excluded);

        return \App::router()->generate($route, $params);
    }

    public function formatPrice($price) {
        return number_format($price, 0, ',', ' ');
    }

    /**
     * @param int   $number  Например: 1, 43, 112
     * @param array $choices Например: ['отзыв', 'отзыва', 'отзывов']
     * @return mixed
     */
    public function numberChoice($number, array $choices) {
        $cases = array (2, 0, 1, 1, 1, 2);

        return $choices[ ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    public function clearZeroValue($value) {
        $frac = $value - floor($value);
        if (0 == $frac) {
            return intval($value);
        } else {
            return rtrim($value, '0');
        }
    }

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

        return 'через ' . $interval->days . ' ' . $this->numberChoice($interval->days, array('день', 'дня', 'дней')) . ' (' . $date->format('d.m.Y') . ')';
    }
}
