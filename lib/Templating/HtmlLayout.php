<?php

namespace Templating;

class HtmlLayout {
    /** @var \Templating\PhpEngine */
    public $engine;
    /** @var string */
    private $templateDir;
    /** @var array */
    protected $params = [];
    /** @var array */
    protected $globalParams = [];
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

    /** Возвращает параметр страницы
     * @param $name string Ключ
     * @param $default mixed Значение, возвращаемое при отстуствии ключа
     * @return mixed
     */
    public function getParam($name, $default = null) {
        if (!array_key_exists($name, $this->params)) {
            \App::logger()->warn(sprintf('Неизвестный параметр шаблона "%s"', $name), ['view']);
        }

        return array_key_exists($name, $this->params) ? $this->params[$name] : $default;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setGlobalParam($name, $value) {
        $this->globalParams[$name] = $value;
        // временный костыль
        \App::closureTemplating()->setParam($name, $value);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasGlobalParam($name) {
        return array_key_exists($name, $this->globalParams);
    }

    /**
     * @param $name
     * @return null
     */
    public function getGlobalParam($name) {
        if (!array_key_exists($name, $this->globalParams)) {
            \App::logger()->warn(sprintf('Неизвестный глобальный параметр шаблона "%s"', $name), ['view']);
        }

        return array_key_exists($name, $this->globalParams) ? $this->globalParams[$name] : null;
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
        $params = array_merge($this->globalParams, $params);
        $params['page'] = $this;
        $params['user'] = \App::user();
        $params['request'] = \App::request();

        return $this->engine->render($template, $params);
    }

    /**
     * @param string $template
     * @param array $params
     * @throws \Exception
     * @return string
     */
    final public function tryRender($template, array $params = []) {
        $return = '';

        try {
            if (!$this->engine->exists($template)) {
                throw new \Exception(sprintf('Шаблон %s не найден', $template));
            }
            $return = $this->render($template, $params);
        } catch (\Exception $e) {
            \App::logger()->error($e, ['view']);
        }

        return $return;
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
     * @param $value mixed
     * @param $encodeDoubleQuotes bool Указывать false, если нужно вывести JSON, как значение js-переменной в HTML (а не в параметр тэга)
     * @return string
     */
    public function json($value, $encodeDoubleQuotes = true) {
        try {
            $return = $encodeDoubleQuotes ? htmlspecialchars(json_encode($value, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, \App::config()->encoding) : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            $return = '';
            \App::logger()->error(['action' => __METHOD__, 'value' => print_r($value, true), 'error' => $e]);
        }

        return $return;
    }

    /**
     * @param string $stylesheet
     */
    public function addStylesheet($stylesheet) {
        try {
            if (0 === strpos($stylesheet, '/')) {
                $stylesheet .= '?t=' . \Util\Application::getVersion();
            }
        } catch (\Exception $e) {
            \App::logger()->error($e, ['view']);
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
            if (0 === strpos($javascript, '/')) {
                $javascript .= '?t=' . \Util\Application::getVersion();
            }
        } catch (\Exception $e) {
            \App::logger()->error($e, ['view']);
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
        //$this->addMeta('title', (string)$title);
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
        if (null === $content) return;

        if (is_scalar($content)) {
            $this->metas[$name] = (string)$content;
        } else {
            \App::logger()->error(['action' => __METHOD__, 'cms.meta' => ['name' => $name, 'content' => $content]], ['view']);
        }
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

            $return .= '<meta name="' . $name .'" content="' . $this->escape($content) . '" />' . "\n";
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

    public function slotMustacheTemplates() {
        $templates = [
            'tpl-cart-kitForm' => 'cart/kitForm2.mustache',
            'tpl-cart-slot-form' => 'cart/slot/form.mustache',
            'tpl-cart-slot-form-result' => 'cart/slot/form/result.mustache',
        ];

        $result = '';
        foreach ($templates as $id => $path) {
            $result .= $this->getMustacheTemplateInsertion($id, $path);
        }

        return $result;
    }

    public function slotUserConfig() {
        // Проверяем заголовок от балансера - если страница попадает под кэширование, то можно рендерить # include virtual
        // В остальных случаях можно обойтись без дополнительного запроса к /ssi.php
        if (\App::request()->headers->get('SSI') == 'on') {
            return \App::helper()->render('__ssi', ['path' => '/user-config']);
        } else {
            return \App::helper()->render('__userConfig');
        }
    }

    protected function getMustacheTemplateInsertion($id, $path) {
        return
            '<script id="' . $id . '" type="text/html">' . "\n" .
            file_get_contents(\App::config()->templateDir . '/' . $path) .
            '</script>' . "\n";
    }
}