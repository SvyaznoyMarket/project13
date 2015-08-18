<?php

namespace view;


class LiteLayout extends \View\Layout
{

    public function __construct() {
        parent::__construct();
        $this->setGlobalParam('menu', (new \View\Menu($this))->generate_new(\App::user()->getRegion()));
        $this->addStylesheet(\App::config()->debug ? '/public/css/global.css' : '/public/css/global.min.css');
        $this->addJavascript('/public/js/core.min.js');
    }

    public function getParams() {
        return $this->params;
    }

    /**
     * @return string
     */
    public function blockHeadJavascript()
    {
        parent::slotHeadJavascript();
    }

    /**
     * @return string
     */
    public function blockStylesheet()
    {
        return parent::slotStylesheet();
    }


    public function blockHead()
    {
        return $this->render('common/_head');
    }

    /** Навигация
     * @return string
     */
    public function blockNavigation() {
        return $this->render('common/_navigation', ['menu' => $this->getGlobalParam('menu')]);
    }

    /** Блок логина
     * @return string
     */
    public function blockAuth()
    {
        return $this->render('common/_auth', ['redirect' => $this->url('homepage')]);
    }

    /**
     * @return string
     */
    public function blockHeader()
    {
        return $this->render('common/_header');
    }

    /**
     * @return string
     */
    public function blockFooter() {
        return $this->render('common/_footer');
    }

    public function blockUserConfig()
    {
        // Проверяем заголовок от балансера - если страница попадает под кэширование, то можно рендерить # include virtual
        // В остальных случаях можно обойтись без дополнительного запроса к /ssi.php
        if (\App::request()->headers->get('SSI') == 'on') {
            return \App::helper()->render('__ssi', ['path' => '/user-config']);
        } else {
            return \App::helper()->render('__userConfig');
        }
    }

    /** Просмотренные товары
     * @return string
     */
    public function blockViewed() {
        return $this->render('common/_products.viewed');
    }

    public function blockFixedUserbar() {
        return '';
    }

    /** Определения модулей
     * @return null|string
     */
    public function blockModulesDefinitions(){
        $file = \App::config()->appDir . '/frontend/modules.json';
        return file_exists($file)
            ? sprintf('<script type="application/json" id="js-modules-definitions">%s</script>', file_get_contents($file))
            : null;
    }

    /** Основной контент страницы
     * @return string
     */
    public function blockContent() {
        return '';
    }

    /** Шаблоны popup-ов
     * @return string
     */
    public function blockPopupTemplates() {
        return $this->tryRender('common/popups/kit')
            . $this->tryRender('common/popups/one.click')
            . $this->tryRender('common/popups/slot');
    }

    public function blockSearch() {
        return '';
    }

}