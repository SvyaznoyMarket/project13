<?php

namespace Templating;

class PhpEngine implements EngineInterface {
    /** @var string */
    private $templateDir;

    /**
     * @param string $templateDir
     */
    public function __construct($templateDir) {
        $this->templateDir = $templateDir;
    }

    /**
     * @param string $template
     * @param array $params
     *
     * @return string
     */
    public function render($template, array $params = []) {
        // render
        extract($params, EXTR_REFS);
        ob_start();
        require $this->templateDir . '/' . $template . '.php';

        return ob_get_clean();
    }

    /**
     * @param $template
     * @return bool
     */
    public function exists($template) {
        return is_file($this->templateDir . '/' . $template . '.php');
    }
}
