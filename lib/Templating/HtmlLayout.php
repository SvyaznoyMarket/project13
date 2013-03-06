<?php

namespace Templating;

class HtmlLayout {
    /** @var \Templating\PhpEngine */
    private $engine;
    /** @var string */
    private $templateDir;
    /** @var array */
    protected $params = [];
    /** @var string */
    protected $title;
    /** @var array */
    protected $metas = [
        'yandex-verification'      => null,
        'viewport'                 => null,
        'title'                    => null,
        'description'              => null,
        'keywords'                 => null,
        //'robots'                   => null,
    ];
    /** @var array */
    protected $stylesheets = [];
    /** @var array */
    protected $javascripts = [];
    /** @var Helper */
    public $helper;

    protected $layout;

    public function __construct() {
        $this->engine = \App::templating();
        $this->helper = new Helper();
    }

    /**
     * @param $name
     * @param $value
     */
    public function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasParam($name) {
        return array_key_exists($name, $this->params);
    }

    /**
     * @param $name
     * @return null
     */
    public function getParam($name) {
        if (!array_key_exists($name, $this->params)) {
            \App::logger()->warn(sprintf('Неизвестный параметр шаблона "%s".', $name));
        }

        return array_key_exists($name, $this->params) ? $this->params[$name] : null;
    }

    protected function prepare() {}

    /**
     * @return string
     */
    final public function show() {
        $this->prepare();

        return $this->render($this->layout);
    }

    /**
     * @param string $template
     * @param array $params
     * @return string
     */
    final public function render($template, array $params = []) {
        $params['page'] = $this;
        $params['user'] = \App::user();
        $params['request'] = \App::request();

        return $this->engine->render($template, $params);
    }

    public function startEscape() {
        ob_start();
    }

    public function endEscape() {
        echo htmlspecialchars(ob_get_clean(), ENT_QUOTES, \App::config()->encoding);
    }

    /**
     * @param $value
     * @return string
     */
    public function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, \App::config()->encoding);
    }

    /**
     * @param $value
     * @return string
     */
    public function json($value) {
        return htmlspecialchars(json_encode($value, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_HEX_APOS), ENT_QUOTES, \App::config()->encoding);
    }

    /**
     * @param string $stylesheet
     */
    public function addStylesheet($stylesheet) {
        try {
            $timestamp = filectime(\App::config()->webDir . '/' . trim($stylesheet, '/'));
            $stylesheet .= '?' . $timestamp;
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $this->stylesheets[] = $stylesheet;
    }

    /**
     * @return array
     */
    public function getStylesheets() {
        return $this->stylesheets;
    }

    /**
     * @param string $javascript
     */
    public function addJavascript($javascript) {
        try {
            $timestamp = filectime(\App::config()->webDir . '/' . trim($javascript, '/'));
            $javascript .= '?' . $timestamp;
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $this->javascripts[] = $javascript;
    }

    /**
     * @return array
     */
    public function getJavascripts() {
        return $this->javascripts;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = (string)$title;
        $this->addMeta('title', (string)$title);
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $name
     * @param string $content
     * @throws \InvalidArgumentException
     */
    public function addMeta($name, $content) {
        if (!array_key_exists($name, $this->metas)) {
            throw new \InvalidArgumentException(sprintf('Неизвестый мета-тег "%s"', $name));
        }

        $this->metas[$name] = (string)$content;
    }

    /**
     * @return array
     */
    public function getMetas() {
        return $this->metas;
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
     * @return string
     */
    public function slotMeta() {
        $return = "\n";
        foreach ($this->metas as $name => $content) {
            if (null == $content) continue;

            $return .= '<meta name="' . $name .'" content="' . $content . '" />' . "\n";
        }

        return $return;
    }

    /**
     * @return string
     */
    public function slotStylesheet() {
        $return = "\n";

        foreach ($this->stylesheets as $stylesheet) {
            $return .= '<link href="' . $stylesheet . '" type="text/css" rel="stylesheet" />' . "\n";
        }

        return $return;
    }

    /**
     * @return string
     */
    public function slotHeadJavascript() {
    }

    /**
     * @return string
     */
    public function slotBodyJavascript() {
        $return = "\n";
        foreach ($this->javascripts as $javascript) {
            $return .= '<script src="' . $javascript . '" type="text/javascript"></script>' . "\n";
        }

        return $return;
    }

    /**
     * @return string
     */
    public function slotContent() {
        return '';
    }
}