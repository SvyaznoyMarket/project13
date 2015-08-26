<?php

namespace Model\Shop\Subway;


class Entity {

    /** @var string */
    public $ui;
    /** @var string */
    public $name;
    /** @var \Model\Shop\Subway\LineEntity */
    public $line;

    public function __construct(array $data = []) {
        if (array_key_exists('ui', $data)) $this->setUi($data['ui']);
        if (array_key_exists('uid', $data)) $this->setUi($data['uid']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('line', $data)) {
            $this->setLine(new LineEntity($data['line']));
        } else if (isset($data['line_name'])) {
            $this->setLine(new LineEntity([
                'name'  => @$data['line_name'],
                'color' => @$data['line_color'],
            ]));
        }
    }

    /**
     * @param \Model\Shop\Subway\LineEntity $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return \Model\Shop\Subway\LineEntity
     */
    public function getLine()
    {
        return $this->line;
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
     * @param string $ui
     */
    public function setUi($ui)
    {
        $this->ui = (string)$ui;
    }

    /**
     * @return string
     */
    public function getUi()
    {
        return $this->ui;
    }

} 