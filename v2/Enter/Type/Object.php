<?php

namespace Enter\Type;

class Object extends Basic {
    /**
     * @param $value
     * @throws \InvalidArgumentException
     */
    final public function setValue($value) {
        if (!is_array($value)) {
            throw new \InvalidArgumentException();
        }

        foreach (get_object_vars($this) as $k => $v) {
            if (!array_key_exists($k, $value)) continue;
            $this->{$k} = $v;
        }
    }

    public function __set($name, $value) {
        throw new \LogicException();
    }

    public function __get($name) {
        throw new \LogicException();
    }
}