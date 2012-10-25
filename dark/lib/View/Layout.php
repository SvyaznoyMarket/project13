<?php

namespace View;

class Layout {
    /** @var \Templating\PhpEngine */
    private $engine;
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
        'keywords'                 => null,
        //'robots'                   => null,
    );
    /** @var array */
    protected $stylesheets = array();
    /** @var array */
    protected $javascripts = array();
    /** @var Helper */
    public $helper;
    /** @var array */
    protected $stylesheetTimestamps = array();
    /** @var array */
    protected $javascriptTimestamps = array();

    protected $layout;

    public function __construct() {
        $this->engine = \App::templating();
        $this->helper = new Helper();

        if (\App::config()->asset['timestampEnabled']) {
            try {
                // css
                $content = file_get_contents(\App::config()->dataDir . '/asset/css-timestamp.json');
                $this->stylesheetTimestamps = json_decode($content, true);
                // js
                $content = file_get_contents(\App::config()->dataDir . '/asset/js-timestamp.json');
                $this->javascriptTimestamps = json_decode($content, true);
            } catch (\Exception $e) {
                \App::logger()->error($e);
            }
        }
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

    final public function render($template, array $params = array()) {
        $params['page'] = $this;
        $params['user'] = \App::user();
        $params['request'] = \App::request();

        return $this->engine->render($template, $params);
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
        $name = preg_replace('/^\/css\//', '', $stylesheet);
        if (isset($this->stylesheetTimestamps[$name])) {
            $stylesheet .= '?' . $this->stylesheetTimestamps[$name];
        }

        $this->stylesheets[] = $stylesheet;
    }

    public function getStylesheets() {
        return $this->stylesheets;
    }

    public function addJavascript($javascript) {
        $name = preg_replace('/^\/js\//', '', $javascript);
        if (isset($this->javascriptTimestamps[$name])) {
            $javascript .= '?' . $this->javascriptTimestamps[$name];
        }
        $this->javascripts[] = $javascript;
    }

    public function getJavascripts() {
        return $this->javascripts;
    }

    public function setTitle($title) {
        $this->title = (string)$title;
        $this->addMeta('title', (string)$title);
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
        $return = "\n";
        foreach ($this->metas as $name => $content) {
            if (null == $return) continue;

            $return .= '<meta name="' . $name .'" content="' . $content . '" />' . "\n";
        }

        return $return;
    }

    public function slotStylesheet() {
        $return = "\n";

        foreach ($this->stylesheets as $stylesheet) {
            $return .= '<link href="' . $stylesheet . '" type="text/css" rel="stylesheet" />' . "\n";
        }

        return $return;
    }

    public function slotJavascript() {
        $return = "\n";
        foreach ($this->javascripts as $javascript) {
            $return .= '<script src="' . $javascript . '" type="text/javascript"></script>' . "\n";
        }

        return $return;
    }

    public function slotContent() {
        return '';
    }
}