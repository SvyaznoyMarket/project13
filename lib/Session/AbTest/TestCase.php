<?php

namespace Session\AbTest;

class TestCase {

    /** @var string */
    private $key;

    /** @var string */
    private $name;

    /** @var string */
    private $traffic;

    public function __construct(array $data = []) {
        if (array_key_exists('token', $data)) $this->key = $data['token'];
        if (array_key_exists('name', $data)) $this->name = $data['name'];
        if (array_key_exists('traffic', $data)) $this->traffic = $data['traffic'];
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTraffic() {
        return $this->traffic;
    }
}
