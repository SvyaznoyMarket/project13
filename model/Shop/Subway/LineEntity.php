<?php

namespace Model\Shop\Subway;

class LineEntity {

    /** @var string */
    private $name;
    /** @var string */
    private $color;

    public function __construct($data = []) {
        if (is_array($data)) {
            if (array_key_exists('name', $data)) $this->setName($data['name']);
            if (array_key_exists('color', $data)) $this->setColor($data['color']);
        }
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = (string)$color;
    }

    /**
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }



} 