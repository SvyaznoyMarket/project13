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
     * @param string $routeName
     * @param array $params
     * @param bool $absolute
     * @return mixed
     */
    public function url($routeName, array $params = [], $absolute = false) {
        return \App::router()->generate($routeName, $params, $absolute);
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
}