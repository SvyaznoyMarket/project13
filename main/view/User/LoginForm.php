<?php

namespace View\User;

class LoginForm extends \Form\FormAbstract {
    /** @var string */
    private $username;
    /** @var string */
    private $password;
    /** @var string */
    public $gaClientId = '';
    /** @var array */
    protected $errors = array(
        'global'   => null,
        'username' => null,
        'password' => null,
    );
    
    /** @inheritdoc */
    protected $route = 'user.login';

    /** @inheritdoc */
    protected $submit = 'Войти';

    public function fromArray(array $data) {
        if (array_key_exists('username', $data)) $this->setUsername($data['username']);
        if (array_key_exists('password', $data)) $this->setPassword($data['password']);
        if (array_key_exists('gaClientId', $data)) $this->gaClientId = $data['gaClientId'];
    }

    /**
     * @param string $password
     */
    public function setPassword($password) {
        if(!$password) {
            $this->setError('password', 'Не указан пароль');
            return;
        }
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
        if(!$username) {
            $this->setError('username', 'Не указан логин');
            return;
        }
        $this->username = trim((string)$username);
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /** @inheritdoc */
    public function __toArray() {
        return [
            'username'  => $this->getUsername(),
            'password'  => $this->getPassword(),
        ];
    }
}