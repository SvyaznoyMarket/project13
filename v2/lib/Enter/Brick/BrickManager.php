<?php

namespace Enter\Brick;

class BrickManager {
    /** @var Config */
    protected $config;
    /** @var array */
    protected $parameters = [];
    /** @var Brick[] */
    protected $bricks = [];

    /**
     * @param Config $config
     */
    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * @param $name
     * @return Brick
     * @throws \Exception
     */
    public function get($name) {
        if (!isset($this->bricks[$name])) {
            /** @var Brick|mixed $brick */
            if (false === (($brick = include $this->config->brickDir . '/' . $name . '/init.php') instanceof Brick)) {
                throw new \Exception(sprintf('Кирпич %s не инициализирован', $name));
            }

            if (!is_callable($brick->controller)) {
                throw new \Exception(sprintf('Неправильный контроллер для кирпича %s', $name));
            }

            if ($brick->controller instanceof \Closure) {
                $brick->requestParameters = (new \ReflectionFunction($brick->controller))->getParameters();
            } else if (is_array($brick->controller)) {
                $brick->requestParameters = (new \ReflectionMethod($brick->controller[0], $brick->controller[1]))->getParameters();
                // TODO: заполнить Config
            } else {
                throw new \Exception();
            }

            $this->bricks[$name] = $brick;
        }

        return $this->bricks[$name];
    }

    /**
     * @param $name
     * @param array $parameters
     * @return mixed
     * @throws \Exception
     */
    public function execute($name, array $parameters = []) {
        $brick = $this->get($name);

        $request = null;
        if (is_object($brick->request)) {
            $request = clone $brick->request;
            foreach (get_object_vars($request) as $k => $v) {
                if (array_key_exists($k, $parameters)) {
                    $request->{$k} = $parameters[$k];
                }
            }
        }

        $response = null;
        if (is_object($brick->response)) {
            $response = clone $brick->response;
        }

        $arguments = [];
        foreach ($brick->requestParameters as $reflectionParameter) {
            if ($request && ('request' === $reflectionParameter->getName())) {
                $arguments[] = $request;
            } else if ($response && ('response' === $reflectionParameter->getName())) {
                $arguments[] = $response;
            } else if ($brick->config && ('config' === $reflectionParameter->getName())) {
                $arguments[] = $brick->config;
            } else if (array_key_exists($reflectionParameter->getName(), $this->parameters)) {
                $arguments[] = $this->parameters[$reflectionParameter->getName()];
            } else if (array_key_exists($reflectionParameter->getName(), $parameters)) {
                $arguments[] = $parameters[$reflectionParameter->getName()];
            } else if ($reflectionParameter->isDefaultValueAvailable()) {
                $arguments[] = $reflectionParameter->getDefaultValue();
            } else {
                throw new \Exception(sprintf('Обработчику %s не передан обязательный параметр %s', $name, $reflectionParameter->getName()));
            }
        }

        try {
            if ($response) {
                call_user_func_array($brick->controller, $arguments);

                return $response;
            } else {
                return call_user_func_array($brick->controller, $arguments);
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function setParameter($name, $value) {
        $this->parameters[$name] = $value;
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }
}