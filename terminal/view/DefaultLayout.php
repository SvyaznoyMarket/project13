<?php

namespace Terminal\View;

class DefaultLayout extends \Templating\HtmlLayout {
    protected $layout  = 'layout-default';

    public function __construct() {
        parent::__construct();

        $this->addJavascript('/js/jquery-1.6.4.min.js');
        $this->addJavascript('/js/terminal/product.js');
    }

    /**
     * @param string $routeName
     * @param array $params
     * @param bool $absolute
     * @return mixed
     */
    public function url($routeName, array $params = [], $absolute = false) {
        $params = array_merge([
            'client_id' => \App::config()->coreV2['client_id'],
            'shop_id'   => \App::config()->region['shop_id'],
        ], $params);

        return \App::router()->generate($routeName, $params, $absolute);
    }
}
