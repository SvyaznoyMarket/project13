<?php
namespace Model\Point;

class Help {
    /** @var string */
    public $url = '';
    /** @var string */
    public $name = '';

    /**
     * @param string $token
     * @return Help|null
     */
    static public function createByPointGroupToken($token) {
        if (strpos($token, 'pickpoint') !== false) {
            $help = new Help();
            $help->url = '/pickpoint-help';
            $help->name = 'Как пользоваться постаматом';
            return $help;
        }

        return null;
    }
}