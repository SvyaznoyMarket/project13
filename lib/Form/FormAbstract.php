<?php

namespace Form;

/**
 * @author vadim.kovalenko
 */
abstract class FormAbstract {

    /**
     * Коллеция ошибок по ключам
     * @var array
     */
    protected $errors = [];
    
    /**
     * Маршрут для формирования uri отправки формы
     * @var string
     */
    protected $route;
    
    /**
     * Дополнительные параметры для route
     * @var array
     */
    protected $routeParams = [];


    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->fromArray($data);
    }
    
    
    /**
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }
    
    /**
     * @param string $route
     * @return \Form\FormAbstract
     */
    public function setRoute($route) {
        $this->route = $route;
        return $this;
    }
    
    
    /**
     * @param $name
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setError($name, $value) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        $this->errors[$name] = $value;
    }


    /**
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getError($name) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        return $this->errors[$name];
    }


    /**
     * @return bool
     */
    public function isValid() {
        foreach ($this->errors as $error) {
            if (null !== $error) {
                return false;
            }
        }
        return true;
    }


    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
}
