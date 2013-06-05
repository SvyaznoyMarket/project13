<?php

namespace View\Refurbished;

class SubscribeForm {
    /** @var string */
    private $email;
    /** @var array */
    private $errors = array(
        'global'  => null,
        'email'   => null,
    );

    public function __construct(array $data = []) {
        $this->fromArray($data);
    }

    public function fromArray(array $data) {
        if (array_key_exists('email', $data)) $this->setEmail($data['email']);
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = trim((string)$email);
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
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
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid() {
        $isValid = true;
        foreach ($this->errors as $error) {
            if (null !== $error) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }
}