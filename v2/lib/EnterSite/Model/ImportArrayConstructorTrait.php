<?php

namespace EnterSite\Model;

trait ImportArrayConstructorTrait {
    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if ((bool)$data) {
            $this->import($data);
        }
    }

    abstract public function import(array $data);
}