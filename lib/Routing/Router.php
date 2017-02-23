<?php

namespace Routing;

class Router {
    /** @var array */
    private $rules;
    /** @var string|null */
    private $prefix;
    /** @var array */
    private $globalParams = [];

    public function __construct(array $rules, $prefix = null, $globalParams = []) {
        $this->rules = $rules;
        $this->prefix = $prefix;
        $this->globalParams = $globalParams;
    }

    /**
     * @return array
     */
    public function getRules() {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getGlobalParams() {
        return $this->globalParams;
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
            if (array_key_exists('method', $rule) && !in_array($method, $rule['method'])) {
                continue;
            }

            foreach ($rule['urls'] as $url) {
                if ($this->prefix) {
                    $url = $this->prefix . $url;
                }
                
                if (false === strpos($url, '{')) {
                    if (mb_strtolower($url) === mb_strtolower($path)) {
                        return [
                            'name' => $routeName,
                            'action' => $rule['action'],
                            'pathVars' => [],
                        ];
                    }
                } else {
                    $patternVarReplaces = [];
                    $patternVarNames = [];
                    preg_match_all('#\{(\w+)\}#', $url, $matches, PREG_SET_ORDER);
                    foreach ($matches as $match) {
                        $patternVarName = $match[1];
                        $patternVarReplaces['{' . $patternVarName . '}'] = isset($rule['require'][$patternVarName]) ? '(' . $rule['require'][$patternVarName] . ')' : '([^\/]+)';
                        if (in_array($patternVarName, $patternVarNames)) {
                            throw new \LogicException(sprintf('Шаблон маршрута "%s" не может содержать более одного объявления переменной "%s".', $url, $patternVarName));
                        }
                        $patternVarNames[] = $patternVarName;
                    }

                    if (!preg_match('#^' . strtr($url, $patternVarReplaces) . '$#is', $path, $matches)) {
                        continue;
                    }

                    $pathVars = array_combine($patternVarNames, array_slice($matches, 1));

                    return [
                        'name' => $routeName,
                        'action' => $rule['action'],
                        'pathVars' => $pathVars,
                    ];
                }
            }
        }

        throw new \Exception\NotFoundException(sprintf('Не найден маршрут для пути "%s".', preg_replace('/[^(\x20-\x7F)]*/', '', $path)));
    }

    /**
     * @param  string $routeName Название маршрута
     * @param  array  $params
     * @param  bool   $absolute
     * @return string
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function generateUrl($routeName, array $params = [], $absolute = false) {
        if (!isset($this->rules[$routeName])) {
            throw new \RuntimeException(sprintf('Неизвестный маршрут "%s".', $routeName));
        }

        if ($this->globalParams) {
            $params += $this->globalParams;
        }

        if (isset($params['#'])) {
            $anchor = '#' . $params['#'];
            unset($params['#']);
        } else {
            $anchor = '';
        }

        $urls = $this->rules[$routeName]['urls'];

        if (isset($this->rules[$routeName]['require'])) {
            $require = $this->rules[$routeName]['require'];
        } else {
            $require = [];
        }

        if (isset($this->rules[$routeName]['outFilters'])) {
            foreach ($this->rules[$routeName]['outFilters'] as $outFilterVarName => $outFilterVarPattern) {
                if (array_key_exists($outFilterVarName, $params) && !preg_match('#^' . $outFilterVarPattern . '$#', $params[$outFilterVarName])) {
                    unset($params[$outFilterVarName]);
                }
            }
        }

        usort($urls, function($a, $b) {
            $countA = substr_count($a, '{');
            $countB = substr_count($b, '{');

            if ($countA == $countB) {
                return 0;
            } else if ($countA < $countB) {
                return 1;
            } else {
                return -1;
            }
        });

        $url = call_user_func(function() use($urls, $require, &$params) {
            foreach ($urls as $url) {
                if ($this->prefix) {
                    $url = $this->prefix . $url;
                }

                if (false === strpos($url, '{')) {
                    return $url;
                } else {
                    $patternVarReplaces = [];
                    $newParams = $params;
                    preg_match_all('#\{(\w+)\}#', $url, $matches, PREG_PATTERN_ORDER);

                    foreach ($matches[1] as $patternVarName) {
                        if (!array_key_exists($patternVarName, $params) || (array_key_exists($patternVarName, $require) && !preg_match('#^' . $require[$patternVarName] . '$#', $params[$patternVarName]))) {
                            break;
                        }

                        $patternVarReplaces['{' . $patternVarName . '}'] = $params[$patternVarName];
                        unset($newParams[$patternVarName]);
                    }

                    if (count($matches[1]) == count($patternVarReplaces)) {
                        $params = $newParams;
                        return strtr($url, $patternVarReplaces);
                    }
                }
            }
        });

        if (!$url) {
            throw new \LogicException('Не найдено подходящего url в маршруте "' . $routeName . '".');
        }

        foreach ($params as $key => $value) {
            if (!$value) {
                unset($params[$key]);
            }
        }

        $query = http_build_query($params, '', '&');
        if ($query) {
            $url .= '?' . $query;
        }

        $url .= $anchor;

        if ($absolute) {
            $request = \App::request();

            if ($request->getHost()) {
                $scheme = $request->getScheme();
                $port = '';
                if ('http' === $scheme && 80 != $request->getPort()) {
                    $port = ':' . $request->getPort();
                } elseif ('https' === $scheme && 443 != $request->getPort()) {
                    $port = ':' . $request->getPort();
                }

                $url = $scheme . '://' . $request->getHost() . $port . $url;
            }
        }

        return $url;
    }
}
