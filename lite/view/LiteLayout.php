<?php

namespace view;


class LiteLayout extends \Templating\HtmlLayout
{

    public function __construct() {
        // подмена пути для шаблонов
        $c = \App::config();
        $c->templateDir = $c->appDir . '/lite/template';

        parent::__construct();

        $this->setGlobalParam('menu', (new \View\Menu($this))->generate_new(\App::user()->getRegion()));
    }

    protected function prepare()
    {
        parent::prepare();
        $this->addStylesheet(\App::config()->debug ? '/public/css/global.css' : '/public/css/global.min.css');
        $this->addJavascript('/public/js/modules.js');
    }

    /**
     * @return string
     */
    public function slotHeadJavascript()
    {
        parent::slotHeadJavascript();
    }

    /** Навигация
     * @return string
     */
    public function slotNavigation() {
        return $this->render('common/_navigation', ['menu' => $this->getGlobalParam('menu')]);
    }


}