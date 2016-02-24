<?php

namespace EnterApplication\Form;

use EnterLab\Form;

class LoginForm extends Form\AbstractForm
{
    /** @var Form\Field */
    public $username;
    /** @var Form\Field */
    public $password;

    public function __construct()
    {
        $this->username = $this->createField('username', Form\Field::TYPE_STRING);
        $this->password = $this->createField('password', Form\Field::TYPE_STRING);
    }

    /**
     * @return $this
     */
    public function validate()
    {
        if (!is_string($this->username->value) || ('' === $this->username->value)) {
            $this->addError('Не указан email или номер телефона', $this->username);
        }

        if (!is_string($this->password->value) || ('' === $this->password->value)) {
            $this->addError('Не указан пароль', $this->password);
        }

        return $this;
    }

    /**
     * @param \Exception $error
     * @return $this
     */
    public function validateByError(\Exception $error)
    {
        switch ($error->getCode()) {
            case 614:
                $this->addError('Пользователь не найден', $this->username);
                break;
            case 689:
                $this->addError('Неправильный email', $this->username);
                break;
            case 690:
                $this->addError('Неправильный телефон', $this->username);
                break;
            case 613:
                $this->addError('Неверный пароль', $this->password);
                break;
            case 609: default:
                $this->addError('Не удалось войти');
                break;
        }

        return $this;
    }
}
