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
        \Debug\Timer::start('mustacheRenderer.get');
        $return = \App::mustache()->render($template, $params);
        \Debug\Timer::stop('mustacheRenderer.get');

        return $return;
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
     * @return mixed
     * @throws \RuntimeException
     */
    public function replacedUrl(array $replaces, array $excluded = null, $route = null) {
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
        foreach ($request->query->all() as $k => $v) {
            $params[$k] = $v;
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

        return \App::router()->generate($route, $params);
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
     * @param $price
     * @param int $numDecimals
     * @param string $decimalsDelimiter
     * @param string $thousandsDelimiter
     * @return string
     */
    public function formatPrice($price, $numDecimals = 0, $decimalsDelimiter = ',', $thousandsDelimiter = ' ') {
        return number_format($price, $numDecimals, $decimalsDelimiter, $thousandsDelimiter);
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
}