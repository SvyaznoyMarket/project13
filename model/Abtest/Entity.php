<?php

namespace Model\Abtest;

class Entity {

    /** @var string */
    private $traffic;

    /** @var string */
    private $key;

    /** @var string */
    private $name;

    /** @var string */
    private $gaEvent;


    public function __construct(array $data = []) {
        if (array_key_exists('traffic', $data)) $this->setTraffic($data['traffic']);
        if (array_key_exists('key', $data)) $this->setKey($data['key']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('ga_event', $data)) $this->setGaEvent($data['ga_event']);
    }

    /**
     * @param string $gaEvent
     */
    public function setGaEvent($gaEvent)
    {
        $this->gaEvent = $gaEvent;
    }

    /**
     * @return string
     */
    public function getGaEvent()
    {
        return $this->gaEvent;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $traffic
     */
    public function setTraffic($traffic)
    {
        $this->traffic = $traffic;
    }

    /**
     * @return string
     */
    public function getTraffic()
    {
        return $this->traffic;
    }


}