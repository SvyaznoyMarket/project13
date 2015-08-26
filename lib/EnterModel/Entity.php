<?php

namespace EnterModel;

class Entity {
    /**
     * @param array $data
     * @return $this
     */
    public function importFromArray(array $data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function exportToArray() {
        return get_object_vars($this);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}