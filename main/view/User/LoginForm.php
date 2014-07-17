<?php

namespace View\User;

class LoginForm extends \Form\FormAbstract {
    /** @var string */
    private $username;
    /** @var string */
    private $password;
    /** @var array */
    protected $errors = array(
        'global'   => null,
        'username' => null,
        'password' => null,
    );
    
    /** @inheritdoc */
    protected $route = 'user.login';

    public function fromArray(array $data) {
        if (array_key_exists('username', $data)) $this->setUsername($data['username']);
        if (array_key_exists('password', $data)) $this->setPassword($data['password']);
    }

    /**
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = trim((string)$password);
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
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
}