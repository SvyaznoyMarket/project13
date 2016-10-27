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
     * @param string|null $routeName Если не задан, то используется текущий
     * @param array $params Если не заданы, то используются текущие
     * @param bool $absolute
     * @return mixed
     */
    public function url($routeName = null, array $params = [], $absolute = false) {
        if ($routeName === null) {
            $routeName = \App::request()->routeName;
        }

        if (!$params) {
            $params = \App::request()->routePathVars->all();
        }

        return \App::router()->generateUrl($routeName, $params, $absolute);
    }

    /**
     * @param array $replaces
     * @param array $excluded
     * @param null $routeName
     * @param array $preservedQueryParams
     * @return mixed
     * @throws \RuntimeException
     */
    public function replacedUrl(array $replaces, array $excluded = [], $routeName = null, array $preservedQueryParams = []) {
        $request = \App::request();

        if (!$routeName) {
            if (!$request->routeName) {
                throw new \RuntimeException('В запросе не задан routeName');
            }

            $routeName = $request->routeName;
        }

        if (!$preservedQueryParams) {
            $params = array_merge($request->routePathVars->all(), $request->query->all());
        } else {
            $params = array_merge($request->routePathVars->all(), call_user_func(function() use($request, $preservedQueryParams) {
                $filteredQuery = [];
                foreach ($request->query->all() as $key => $value) {
                    if (in_array($key, $preservedQueryParams)) {
                        $filteredQuery[$key] = $value;
                    }
                }

                return $filteredQuery;
            }));
        }

        foreach ($replaces as $k => $v) {
            if (preg_match('/([^\[]+)\[([^\[]+)\]/', $k, $matches)) {
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

        $params = array_diff_key($params, array_fill_keys($excluded, null));

        return \App::router()->generateUrl($routeName, $params);
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
    public function unescape($value) {
        // Без ENT_HTML5 не преобразуется сущность &apos; (и, возможно, другие)
        return htmlspecialchars_decode($value, ENT_QUOTES | ENT_HTML5);
    }

    /**
     * @param $value
     * @return string
     */
    public function json($value) {
        return htmlspecialchars(json_encode($value, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_HEX_APOS), ENT_QUOTES, 'UTF-8');
    }

    public function jsonInScriptTag($value, $id = '', $class = '') {
        return sprintf('<script id="%s" class="%s" type="application/json">%s</script>', $id, $class, json_encode($value, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param float|int|string $price
     * @return string
     */
    public function formatPrice($price) {
        $price = str_replace(',', '.', $price);
        $price = preg_replace('/\s/', '', $price);
        $price = number_format((float)$price, 2, '.', '');
        $price = explode('.', $price);

        /* Маленькие пробелы между разрядами целой части цены */
        if (strlen($price[0]) >= 5) {
            $price[0] = preg_replace('/(\d)(?=(\d\d\d)+([^\d]|$))/', '$1 ', $price[0]); // в замене используется тонкий пробельный символ U+2009
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
     * @param string $text
     * @param string $charset
     * @return string
     */
    public function lcfirst($text, $charset = 'UTF-8') {
        return mb_strtolower(mb_substr($text, 0, 1, $charset), $charset) . mb_substr($text, 1, mb_strlen($text, $charset) - 1, $charset);
    }

    /**
     * @param \DateTime $date
     * @param string $format Формат для возврата
     * @return string
     */
    public function humanizeDate(\DateTime $date, $format = 'd.m.Y') {
        $formatted = $date->format($format);

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

            if ($formatted == $now->format($format)) {
                return $name;
            }
        }

        return $formatted;
    }

    /** Заменяет пробелы после точки неразрывным юникодных пробелом U+00A0
     * @param $string string
     * @return string
     */
    public function noBreakSpaceAfterDot($string) {
        return preg_replace('/\.\s+/', '. ', $string);
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

    /**
     * @return string
     */
    public function getCurrentSort() {
        $request = \App::request();
        $productSorting = new \Model\Product\Sorting();
        
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->query->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);
        
        if (!$productSorting->isDefault()) {
            $active = $productSorting->getActive();
            return 'sort=' . urlencode(implode('-', [$active['name'], $active['direction']]));
        }

        return '';
    }
}