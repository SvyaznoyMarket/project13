<?php

namespace Enter\Templating\PhpClosure;

class Renderer {
    /** @var Config */
    private $config;
    /** @var \Closure[] */
    protected $closures = [];

    /**
     * @param Config $config
     */
    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * @param $template
     * @param $context
     * @return string
     * @throws \Exception
     */
    public function render($template, $context = null) {

        if (!isset($this->closures[$template])) {
            $closure = include $this->config->templateDir . '/' . $template . '.php';
            if (false === $closure) {
                throw new \Exception(sprintf('Шаблон %s не найден', $template));
            }
            if (!$closure instanceof \Closure) {
                throw new \Exception(sprintf('Шаблон %s не является функцией', $template));
            }

            $this->closures[$template] = $closure;
        } else {
            $closure = $this->closures[$template];
        }

        ob_start();

        try {
            call_user_func($closure, $context);
        } catch (\Exception $e) {
            // TODO: журналирование
        }

        return ob_get_clean();
    }

    public function clean() {
        $previous = null;
        while (($level = ob_get_level()) > 0 && $level !== $previous) {
            $previous = $level;
            ob_end_clean();
        }

        return $this;
    }
}
