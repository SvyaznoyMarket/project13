<?php

namespace Helper;

class TemplateHelper {
    /**
     * @param string $template
     * @param array $params
     * @return string
     */
    public function render($template, $params = []) {
        return \App::closureTemplating()->render($template, $params);
    }

    /**
     * @param string $template
     * @param array $params
     * @return string
     */
    public function renderWithMustache($template, $params = []) {
        return \App::mustache()->render($template, $params);
    }

    /**
     * @param string $routeName
     * @param array $params
     * @param bool $absolute
     * @return mixed
     */
    public function url($routeName, array $params = [], $absolute = false) {
        return \App::router()->generate($routeName, $params, $absolute);
    }

    /**
     * @param array $replaces
     * @param array $excluded
     * @param null $route
     * @param bool|array $keepQueryStringParams
     * @param null|string $baseUrl
     * @return mixed
     * @throws \RuntimeException
     */
    public function replacedUrl(array $replaces, array $excluded = null, $route = null, $keepQueryStringParams = true, $baseUrl = null) {
        $request = \App::request();

        if (null == $route) {
            if (!$request->attributes->has('route')) {
                throw new \RuntimeException('В атрибутах запроса не задан параметр route');
            }

            $route = $request->attributes->get('route');
        }

        $excluded = (null == $excluded) ? ['page' => '1'] : $excluded;

        $params = [];
        foreach (array_diff(array_keys($request->attributes->all()), ['pattern', 'method', 'action', 'route', 'require']) as $k) {
            $params[$k] = $request->attributes->get($k);
        }

        if ($keepQueryStringParams) {
            foreach ($request->query->all() as $k => $v) {
                if (!is_array($keepQueryStringParams) || in_array($k, $keepQueryStringParams, true)) {
                    $params[$k] = $v;
                }
            }
        }

        foreach ($replaces as $k => $v) {
            if(preg_match('/([^\[]+)\[([^\[]+)\]/', $k, $matches)) {
                $mainKey = $matches[1];
                $subKey = $matches[2];

                if (null === $v) {
                    if (isset($params[$mainKey][$subKey])) unset($params[$mainKey][$subKey]);
                    continue;
                }

                $params[$mainKey][$subKey] = $v;
            } else {
                if (null === $v) {
                    if (isset($params[$k])) unset($params[$k]);
                    continue;
                }

                $params[$k] = $v;
            }
        }

        $params = array_diff_assoc($params, $excluded);

        $url = \App::router()->generate($route, $params);

        if ($baseUrl) {
            $urlQueryString = $this->getQueryString($url);
            if ($urlQueryString) {
                $baseUrlQueryString = $this->getQueryString($baseUrl);
                parse_str($urlQueryString, $urlParams);
                parse_str($baseUrlQueryString, $baseUrlParams);
                $resultParams = array_merge($baseUrlParams, $urlParams);

                if ($baseUrlQueryString) {
                    $baseUrl = substr($baseUrl, 0, -(strlen($baseUrlQueryString) + 1));
                }

                $url = $baseUrl . ($resultParams ? '?' . http_build_query($resultParams) : '');
            } else {
                $url = $baseUrl;
            }
        }

        return $url;
    }

    private function getQueryString($url) {
        $pos = strpos($url, '?');
        if ($pos === false) {
            return '';
        } else {
            return substr($url, $pos + 1);
        }
    }

    /**
     * @param $name
     * @return null
     */
    public function getParam($name) {
        return \App::closureTemplating()->getParam($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasParam($name) {
        return \App::closureTemplating()->hasParam($name);
    }

    /**
     *
     */
    public function startEscape() {
        ob_start();
    }

    /**
     *
     */
    public function endEscape() {
        echo htmlspecialchars(ob_get_clean(), ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param $value
     * @return string
     */
    public function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param $value
     * @return string
     */
    public function json($value) {
        return htmlspecialchars(json_encode($value, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_HEX_APOS), ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param float|int|string $price
     * @return string
     */
    public function formatPrice($price) {
        $price = str_replace(',', '.', $price);
        $price = preg_replace('/\s/', '', $price);
        $price = number_format($price, 2, '.', '');
        $price = explode('.', $price);

        /* Маленькие пробелы между разрядами целой части цены */
        if (strlen($price[0]) >= 5) {
            $price[0] = preg_replace('/(\d)(?=(\d\d\d)+([^\d]|$))/', '$1&thinsp;', $price[0]); // TODO: заменить &thinsp; на соответствующий unicode символ
        }

        if (isset($price[1]) && $price[1] == 0) {
            unset($price[1]);
        }

        return implode('.', $price);
    }

    /**
     * @param int   $number  Например: 1, 43, 112
     * @param array $choices Например: ['отзыв', 'отзыва', 'отзывов']
     * @return mixed
     */
    public function numberChoice($number, array $choices) {
        $cases = [2, 0, 1, 1, 1, 2];

        return $choices[ ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * @param int   $number  Например: 1, 43, 112
     * @param array $choices Например: ['отзыв', 'отзыва', 'отзывов']
     * @param string $wordsBetween Например 'прекрасных'
     * @return string '3 прекрасных отзыва'
     */
    public function numberChoiceWithCount($number, array $choices, $wordsBetween = '') {
        return preg_replace('/\s+/', ' ', $number.' '.$wordsBetween.' '.$this->numberChoice($number, $choices));
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
            return (float)rtrim($value, '0');
        }
    }

    /**
     * @param string $text
     * @param string $cp
     * @return string
     */
    public static function mbyte_ucfirst($text, $cp = 'UTF-8') {
        return mb_strtoupper( mb_substr( $text, 0, 1, $cp ), $cp ) . mb_strtolower( mb_substr( $text, 1, mb_strlen( $text, $cp ) - 1, $cp ), $cp );
    }

    /**
     * @param \DateTime $date
     * @return string
     */
    public function humanizeDate(\DateTime $date) {
        $formatted = $date->format('d.m.Y');

        $namesByDay = [
            0 => 'Сегодня',
            1 => 'Завтра',
            2 => 'Послезавтра',
        ];

        $now = new \DateTime('now');

        foreach ($namesByDay as $day => $name) {
            if ($day > 0) {
                $now->modify('+1 day');
            }

            if ($formatted == $now->format('d.m.Y')) {
                return $name;
            }
        }

        return $formatted;
    }

    /** Возвращает номер телефона в зависимости от региона
     * @return string
     */
    public function regionalPhone() {
        $config = \App::config();
        $region = \App::user()->getRegion();
        if (!$region) return $config->company['phone'];
        if ($region->getId() == 108136) return $config->company['spbPhone'];
        if ($region->getId() == 14974) return $config->company['moscowPhone'];
        return $config->company['phone'];
    }

}