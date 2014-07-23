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
     * Название кнопки отправки формы
     * @var string
     */
    protected $submit;


    /**
     * @return array
     */
    abstract public function __toArray();

    /**
     * @param $data
     * @return mixed
     */
//    abstract public function fromArray($data);

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->fromArray($data);
    }

    /**
     * @return string
     */
    public function getSubmit() {
        return ($this->submit?$this->submit:'Подтвердить');
    }

    /**
     * @param string $submit
     */
    public function setSubmit($submit) {
        $this->submit = $submit;
        return $this;
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


    /**
     * Описание состояния формы
     * @return array
     */
    public function getState() {
        $fields = [];
        $data   = $this->__toArray();
        $errors = $this->getErrors();

        foreach($data as $k=>$v) {
            $fields[$k] = [
                'value'     => $v,
                'error'     => (isset($errors[$k])?$errors[$k]:null)
            ];
        }

        return [
            'route'     => $this->getRoute(),
            'submit'    => $this->getSubmit(),
            'isValid'   => $this->isValid(),
            'fields'    => $fields,
            'error'     => ($errors['global']?$errors['global']:null)
        ];
    }
}
