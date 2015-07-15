<?php

namespace view;


/** Сделан для совместимости
 * Class Layout
 * @package view
 */
class Layout extends \Templating\HtmlLayout
{

    public function __construct()
    {
        // подмена пути для шаблонов
        $config = \App::config();
        $config->templateDir = $config->appDir . '/lite/template';

        parent::__construct();
    }

}