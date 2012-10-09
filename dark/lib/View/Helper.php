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

    public function formatNumberChoice($text, array $replaces = array(), $number) {
        static $instance;

        if (!$instance) {
            $instance = new \Util\ChoiceFormatter();
        }

        if ((bool)$replaces) {
            $text = strtr($text, $replaces);
        }

        return $instance->format($text, $number);
    }
}
