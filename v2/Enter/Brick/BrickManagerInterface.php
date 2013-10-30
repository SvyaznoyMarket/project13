<?php

namespace Enter\Brick;

interface BrickManagerInterface {
    /**
     * @param $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param $name
     * @param array $parameters
     * @return mixed
     */
    public function execute($name, array $parameters = []);
}