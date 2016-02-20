<?php

namespace EnterLab\Form;

abstract class AbstractForm
{
    /** @var Error[] */
    public $errors = [];
    /** @var Field[] */
    private $fieldsByName = [];

    /**
     * @param $name
     * @param string $type
     * @param mixed $value
     * @return Field
     */
    final public function createField($name, $type = Field::TYPE_STRING, $value = null)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException();
        }

        if (isset($this->fieldsByName[$name])) {
            throw new \InvalidArgumentException(sprintf('Поле %s уже создано', $name));
        }

        $field = new Field();
        $field->name = $name;
        $field->type = $type;
        $field->value = $value;

        $this->fieldsByName[$name] = $field;

        return $field;
    }

    /**
     * @param $message
     * @param Field|string|null $field
     * @param string|null $code
     * @return Error
     */
    final public function addError($message, $field = null, $code = null)
    {
        if (!is_string($message)) {
            throw new \InvalidArgumentException();
        }

        if (!$field instanceof Field) {
            $field = $this->getField($field);
        }

        $error = new Error();
        $error->code = $code;
        $error->message = $message;
        $error->field = $field ? $field->name : null;

        $this->errors[] = $error;

        if ($field) {
            $field->error = $error;
        }

        return $error;
    }

    /**
     * Удаляет ошибки формы
     */
    final public function clearErrors()
    {
        $this->errors = [];

        foreach ($this->getFields() as $field) {
            $field->error = null;
        }
    }

    /**
     * Обнуляет все значения полей формы
     */
    final public function clearValue()
    {
        foreach ($this->getFields() as $field) {
            $field->value = null;
        }
    }

    /**
     * Получает поле по имени
     *
     * @param $name
     * @return Field|null
     */
    final public function getField($name)
    {
        return isset($this->fieldsByName[$name]) ? $this->fieldsByName[$name] : null;
    }

    /**
     * @return Field[]
     */
    final public function getFields()
    {
        return $this->fieldsByName;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fromArray(array $data) {
        foreach ($data as $k => $v) {
            if ($field = $this->getField($k)) {
                switch ($field->type) {
                    case Field::TYPE_STRING:
                        $field->value = is_scalar($v) ? (string)$v : null;
                        break;
                    case Field::TYPE_INTEGER:
                        $field->value = is_scalar($v) ? (int)$v : null;
                        break;
                    case Field::TYPE_FLOAT:
                        $field->value = is_scalar($v) ? (float)$v : null;
                        break;
                    case Field::TYPE_ARRAY:
                        $field->value = is_array($v) ? $v : null;
                        break;
                    case Field::TYPE_MOBILE_PHONE_NUMBER:
                        $value = $v;
                        $value = preg_replace('/^\+7/', '8', $value);
                        $value = preg_replace('/[^\d]/', '', $value);
                        $field->value = (11 === strlen($value)) ? $value : null;
                        break;
                }
            }
        }

        return $this;
    }

    /**
     * Проверяет поля формы и устанавливает ошибки формы
     *
     * @return $this
     */
    abstract public function validate();

    /**
     * Проверяет ошибку по коду и устанавливает ошибки формы
     *
     * @param \Exception $error
     * @return $this
     */
    abstract public function validateByError(\Exception $error);
}