<?php

namespace View\User;

class RegistrationForm {
    /** @var string */
    private $username;
    /** @var string */
    private $firstName;
    /** @var array */
    private $errors = array(
        'global'     => null,
        'username'   => null,
        'first_name' => null,
    );

    public function __construct(array $data = array()) {
        $this->fromArray($data);
    }

    public function fromArray(array $data) {
        if (array_key_exists('username', $data)) $this->setUsername($data['username']);
        if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = trim((string)$firstName);
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = trim((string)$username);
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
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
}