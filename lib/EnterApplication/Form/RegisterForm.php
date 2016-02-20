<?php

namespace EnterApplication\Form;

use EnterLab\Form;

class RegisterForm extends Form\AbstractForm
{
    /** @var Form\Field */
    public $firstName;
    /** @var Form\Field */
    public $email;
    /** @var Form\Field */
    public $phoneNumber;
    /** @var Form\Field */
    public $agreed;

    public function __construct()
    {
        $this->firstName = $this->createField('first_name', Form\Field::TYPE_STRING);
        $this->email = $this->createField('email', Form\Field::TYPE_STRING);
        $this->phoneNumber = $this->createField('phone', Form\Field::TYPE_STRING);
        $this->agreed = $this->createField('agreed', Form\Field::TYPE_STRING);
    }

    /**
     * @return $this
     */
    public function validate()
    {
        if (mb_strlen($this->firstName->value) < 2) {
            $this->addError('Не указано имя', $this->firstName);
        }

        if (false === strpos($this->email->value, '@')) {
            $this->addError('Не указан email', $this->email);
        }

        if (!$this->agreed->value) {
            $this->addError('Не указано согласие', $this->agreed);
        }

        if (!$this->phoneNumber->value) {
            //$this->addError('Не указан номер телефона', $this->phoneNumber);
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
            case 689:
                $this->addError('Такой email уже зарегистрирован', $this->email);
                break;
            case 684:
                $this->addError('Неправильный email', $this->email);
                break;
            case 690:
                $this->addError('Такой телефон уже зарегистрирован', $this->phoneNumber);
                break;
            case 686:
                $this->addError('Неправильный номер телефона', $this->phoneNumber);
                break;
            case 609: case 680: default:
                $this->addError('Не удалось пройти регистрацию');
                break;
        }

        return $this;
    }
}
