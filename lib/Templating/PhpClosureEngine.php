<?php

namespace Templating;

class PhpClosureEngine implements EngineInterface {
    /** @var string */
    private $templateDir;
    /** @var array */
    protected $params = [];
    /** @var \Closure[] */
    protected $closures = [];

    /**
     * @param string $templateDir
     */
    public function __construct($templateDir) {
        $this->templateDir = $templateDir;
    }

    /**
     * @param $template
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function render($template, array $params = []) {
        \Debug\Timer::start('renderer.get');

        if (!isset($this->closures[$template])) {
            $closure = include $this->templateDir . '/' . $template . '.php';
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

        $reflectedFunction = new \ReflectionFunction($closure);

        $arguments = [];
        foreach ($reflectedFunction->getParameters() as $reflectedParameter) {
            if (array_key_exists($reflectedParameter->getName(), $this->params)) {
                $arguments[] = $this->params[$reflectedParameter->getName()];
            } else if (array_key_exists($reflectedParameter->getName(), $params)) {
                $arguments[] = $params[$reflectedParameter->getName()];
            } else if ($reflectedParameter->isDefaultValueAvailable()) {
                $arguments[] = $reflectedParameter->getDefaultValue();
            } else {
                throw new \Exception(sprintf('Шаблону %s не передан обязательный параметр %s', $template, $reflectedParameter->getName()));
            }
        }

        ob_start();

        try {
            call_user_func_array($closure, $arguments);
        } catch (\Exception $e) {
            throw $e;
        }

        \Debug\Timer::stop('renderer.get');

        return ob_get_clean();
    }

    /**
     *
     */
    public function clean() {
        $previous = null;
        while (($level = ob_get_level()) > 0 && $level !== $previous) {
            $previous = $level;
            ob_end_clean();
        }

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setParam($name, $value) {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * @param $name
     * @return null
     */
    public function getParam($name) {
        return array_key_exists($name, $this->params) ? $this->params[$name] : null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasParam($name) {
        return array_key_exists($name, $this->params);
    }
}
