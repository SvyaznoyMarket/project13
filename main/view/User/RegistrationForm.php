<?php

namespace View\User;

class RegistrationForm {
    /** @var string */
    private $username;
    /** @var string */
    private $firstName;
    /** @var string */
    private $email;
    /** @var string */
    private $phone;

    /** @var array */
    private $errors = array(
        'global'     => null,
        'username'   => null,
        'first_name' => null,
        'email'      => null,
        'phone'      => null,
        'agreed'     => null,
    );

    public function __construct(array $data = []) {
        $this->fromArray($data);
    }

    public function fromArray(array $data) {
        if (array_key_exists('username', $data)) $this->setUsername($data['username']);
        // TODO: осторожно, опасно!
        if ($this->getUsername()) {
            if (strpos($this->getUsername(), '@')) {
                $this->setEmail($this->getUsername());
            } else {
                $this->setPhone($this->getUsername());
            }
        }

        if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
        if (array_key_exists('email', $data)) $this->setEmail($data['email']);
        if (array_key_exists('phone', $data)) $this->setPhone($data['phone']);
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
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email ? trim((string)$email) : null;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone) {
        $this->phone = $phone ? trim((string)$phone) : null;
    }

    /**
     * @return string
     */
    public function getPhone() {
        return $this->phone;
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