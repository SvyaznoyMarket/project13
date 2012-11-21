<?php

class SubwayEntity
{
    /* @var int */
    private $id;
    /* @var string */
    private $name;
    /** @var int */
    private $region_id;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('geo_id', $data)) $this->setRegionId($data['geo_id']);
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $region_id
     */
    public function setRegionId($region_id)
    {
        $this->region_id = (int)$region_id;
    }

    /**
     * @return int
     */
    public function getRegionId()
    {
        return $this->region_id;
    }

}
