<?php

namespace Config\Oauth;

class OdnoklassnikiConfig {
    /**
     * @var string ID приложения
     */
    public $clientId;
    /**
     * @var string Секретный ключ
     */
    public $secretKey;
    /**
     * @var string Публичный ключ
     */
    public $publicKey;

    public function __set($name, $value) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }

    public function __get($name) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }
}