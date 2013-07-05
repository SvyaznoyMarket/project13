<?php

namespace Routing;

class Router {
    /** @var array */
    private $rules;
    /** @var string|null */
    private $prefix;

    public function __construct(array $rules, $prefix = null) {
        $this->rules = $rules;
        $this->prefix = $prefix ? ('/' . trim($prefix, '/')) : '';
    }

    /**
     * @param string $path
     * @param string $method
     * @return array
     * @throws \LogicException
     * @throws \Exception\NotFoundException
     */
    public function match($path, $method) {
        $path = rawurldecode($path);

        foreach ($this->rules as $routeName => $rule) {
            if ($this->prefix) {
                $rule['pattern'] = $this->prefix . $rule['pattern'];
            }

            // Если не указан http-метод или http-метод совпадает с правилом маршрута ...
            if (!array_key_exists('method', $rule) || in_array($method, $rule['method'])) {

                // Если в шаблоне нет переменных ...
                if (false === strpos($rule['pattern'], '{')) {
                    if ($rule['pattern'] == $path) {
                        $rule['route'] = $routeName;

                        return $rule;
                    }
                } // ... иначе
                else {
                    $patternReplaces = [];
                    $varNames = [];
                    preg_match_all('#\{(\w+)\}#', $rule['pattern'], $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
                    foreach ($matches as $match) {
                        $varName = $match[1][0];
                        $patternReplaces['{' . $varName . '}'] = isset($rule['require'][$varName]) ? ('('.$rule['require'][$varName].')') : '([^\/]+)';
                        if (in_array($varName, $varNames)) {
                            throw new \LogicException(sprintf('Шаблон маршрута "%s" не может содержать более одного объявления переменной "%s".', $rule['pattern'], $varName));
                        }
                        $varNames[] = $varName;
                    }
                    $pattern = '#^' . strtr($rule['pattern'], $patternReplaces) . '$#s';

                    if (!preg_match($pattern, $path, $matches)) {
                        continue;
                    }

                    $vars = array_combine($varNames, array_slice($matches, 1));
                    $rule['route'] = $routeName;

                    return array_merge($vars, $rule);
                }
            }
        }

        throw new \Exception\NotFoundException(sprintf('Не найден маршрут для пути "%s".', preg_replace('/[^(\x20-\x7F)]*/', '', $path)));
    }

    /**
     * @param  string $name Название маршрута
     * @param  array  $params
     * @param  bool   $absolute
     * @return string
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function generate($name, array $params = [], $absolute = false) {
        if (!isset($this->rules[$name])) {
            throw new \RuntimeException(sprintf('Неизвестный маршрут "%s".', $name));
        }

        $rule = $this->rules[$name];
        if ($this->prefix) {
            $rule['pattern'] = $this->prefix . $rule['pattern'];
        }
        $vars = [];

        if (isset($params['#'])) {
            $anchor = '#' . $params['#'];
            unset($params['#']);
        } else {
            $anchor = '';
        }

        // если в шаблоне нет переменных ...
        if (false === strpos($rule['pattern'], '{')) {
            $url = $rule['pattern'];
        // ... иначе
        } else {
            $patternReplaces = [];
            preg_match_all('#\{(\w+)\}#', $rule['pattern'], $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            foreach ($matches as $match) {
                $varName = $match[1][0];
                if (!array_key_exists($varName, $params)) {
                    throw new \LogicException(sprintf('Маршрут "%s" требует параметра "%s".', $name, $varName));
                }
                $patternReplaces['{' . $varName . '}'] = $params[$varName];
                $vars[$varName] = $params[$varName];
            }

            $url = strtr($rule['pattern'], $patternReplaces);
        }

        $extra = array_diff_key($params, $vars);
        if ((bool)$extra && $query = http_build_query($extra, '', '&')) {
            $url .= '?' . $query;
        }

        $url .= $anchor;

        if ($absolute) {
            $request = \App::request();

            if ($request->getHost()) {
                $scheme = $request->getScheme();
                $port = '';
                if ('http' === $scheme && 80 != $request->getPort()) {
                    $port = ':'.$request->getPort();
                } elseif ('https' === $scheme && 443 != $request->getPort()) {
                    $port = ':'.$request->getPort();
                }

                $url = $scheme.'://'.$request->getHost().$port.$url;
            }
        }

        return $url;
    }
}
