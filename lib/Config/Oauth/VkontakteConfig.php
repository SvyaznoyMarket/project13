<?php

namespace Config\Oauth;

class VkontakteConfig {
    /**
     * @var string ID приложения
     */
    public $clientId;
    /**
     * @var string Защищенный ключ
     */
    public $secretKey;

    public function __set($name, $value) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }

    public function __get($name) {
        throw new \LogicException(sprintf('Неизвестный параметр "%s".', $name));
    }
}