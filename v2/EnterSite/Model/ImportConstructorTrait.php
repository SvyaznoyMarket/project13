<?php

namespace EnterSite\Model;

/**
 * @method import
 */
trait ImportConstructorTrait {
    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if ((bool)$data) {
            $this->import($data);
        }
    }
}