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


    /**
     * @param $content
     * @param string $tag
     * @param null $class
     * @return string
     */
    public function wrap( &$content, $attr, $tag = 'div') {
        if ( !empty($tag) ) {
            $res = '<'.$tag;

            if ($attr) {
                if (is_string($attr)){
                    if ( !empty($attr) ) $res .= ' class='.$attr;
                }else
                if ( is_array($attr) ) {
                    foreach($attr as $key => $val):
                        if ( !empty($key) and !empty($val) ):
                            $res .= ' '.$key = $val;
                        endif;
                    endforeach;
                }
            }

            $res .= '>';
            $res .= (string) $content;
            $res .= '</'.$tag.'>';
            return $res;
        }
        return $content;
    }

}