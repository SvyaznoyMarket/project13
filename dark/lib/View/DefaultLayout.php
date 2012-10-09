<?php

namespace View;

class DefaultLayout {
    /** @var string */
    private $templateDir;
    /** @var array */
    protected $params = array();
    /** @var string */
    protected $title;
    /** @var array */
    protected $metas = array(
        'yandex-verification'      => null,
        'viewport'                 => null,
        'title'                    => null,
        'description'              => null,
        //'keywords'                 => null,
        //'robots'                   => null,
    );
    /** @var array */
    protected $stylesheets = array();
    /** @var array */
    protected $javascripts = array();
    /** @var string */
    protected $layout  = 'layout-default-twoColumn';
    /** @var Helper */
    public $helper;

    public function __construct() {
        $this->templateDir = \App::config()->dataDir . '/template';

        $this->helper = new Helper();

        $this->setTitle('Enter');
        $this->addMeta('yandex-verification', 'enter');
        $this->addMeta('viewport', 'width=900');
        $this->addMeta('title', 'Enter');
        $this->addMeta('description', 'Enter');

        $this->addStylesheet('/css/skin/inner.css?2012-10-01');
        $this->addStylesheet('/css/jquery-ui-1.8.20.custom.css');

        $this->addJavascript('/js/jquery-1.6.4.min.js');
        $this->addJavascript('/js/LAB.min.js');
        $this->addJavascript('/js/loadjs.js');
    }

    public function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    public function hasParam($name) {
        return array_key_exists($name, $this->params);
    }

    public function getParam($name) {
        /*
        if (!array_key_exists($name, $this->params)) {
            throw new \Exception(sprintf('Неизвестный параметр "%s".', $name));
        }
        */

        return array_key_exists($name, $this->params) ? $this->params[$name] : null;
    }

    protected function prepare() {}

    final public function show() {
        $this->prepare();

        return $this->render($this->layout);
    }

    final public function render($template, array $vars = array()) {
        \Debug\Timer::start('template:' . $template);
        \App::logger('view')->info('Start render template '.$template);

        // render
        extract($vars, EXTR_REFS);
        $page = $this;
        $user = \App::user();
        ob_start();
        require $this->templateDir . '/' . $template . '.php';

        $return = ob_get_clean();

        $spend = \Debug\Timer::stop('template:' . $template);
        \App::logger('view')->info('End render template '.$template.' '.$spend);

        return $return;
    }

    public function startEscape() {
        ob_start();
    }

    public function endEscape() {
        echo htmlspecialchars(ob_get_clean(), ENT_QUOTES, 'UTF-8');
    }

    public function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function addStylesheet($stylesheet) {
        $this->stylesheets[] = $stylesheet;
    }

    public function getStylesheets() {
        return $this->stylesheets;
    }

    public function addJavascript($javascript) {
        $this->javascripts[] = $javascript;
    }

    public function getJavascripts() {
        return $this->javascripts;
    }

    public function setTitle($title) {
        $this->title = (string)$title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function addMeta($name, $content) {
        if (!array_key_exists($name, $this->metas)) {
            throw new \InvalidArgumentException(sprintf('Неизвестый мета-тег "%s"', $name));
        }

        $this->metas[$name] = (string)$content;
    }

    public function getMetas() {
        return $this->metas;
    }

    public function url($routeName, array $params = array(), $absolute = false) {
        return \App::router()->generate($routeName, $params, $absolute);
    }

    public function slotMeta() {
        $metas = array();
        foreach ($this->metas as $name => $content) {
            if (null !== $content) $metas[$name] = $content;
        }

        return $this->render('_meta', array('metas' => $metas));
    }

    public function slotStylesheet() {
        return $this->render('_stylesheet', array('stylesheets' => $this->getStylesheets()));
    }

    public function slotJavascript() {
        return $this->render('_javascript', array('javascripts' => $this->getJavascripts()));
    }

    public function slotRelLink() {
        return '';
    }

    public function slotGoogleAnalytics() {
        return (\App::config()->googleAnalytics['enabled']) ? $this->render('_googleAnalytics') : '';
    }

    public function slotBodyDataAttribute() {
        return 'default';
    }

    public function slotHeader() {
        if (!$this->hasParam('rootCategories')) {
            $rootCategories = \RepositoryManager::getProductCategory()->getRootCollection();
            foreach($rootCategories as $i => $category){
                if(!$category->getIsInMenu()){
                    unset($rootCategories[$i]);
                }
            }
            $this->setParam('rootCategories', $rootCategories);
        }

        return $this->render('_header', $this->params);
    }

    public function slotFooter() {
        $client = \App::contentClient();
        $response = @$client->send('footer_default', array('shop_count' => \App::coreClientV2()->query('shop/get-quantity')));

        return $response['content'];
    }

    public function slotContentHead() {
        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }
        // навигация
        if (!$this->hasParam('breadcrumbs')) {
            $this->setParam('breadcrumbs', array());
        }

        return $this->render('_contentHead', $this->params);
    }

    public function slotContent() {
        return '';
    }

    public function slotSidebar() {
        return '';
    }

    public function slotRegionSelection() {
        return '';
    }

    public function slotInnerJavascript() {
        return $this->render('_innerJavascript');
    }

    public function slotAuth() {
        return '';
    }

    public function slotYandexMetrika() {
        return (\App::config()->yandexMetrika['enabled']) ? $this->render('_yandexMetrika') : '';
    }
}
