<?php

namespace Debug;

class Collector {
    const TYPE_ERROR = 'error';
    const TYPE_WARN = 'warn';
    const TYPE_INFO = 'info';

    private $data = array();

    public function add($name, $value, $priority = null, $type = null) {
        static $next = 50;

        $this->data[] = array(
            'name'     => $name,
            'value'    => $value,
            'type'     => $type ?: self::TYPE_INFO,
            'priority' => $priority ?: $next,
        );

        --$next;
    }

    public function getAll() {
        usort($this->data, function(array $a, array $b) {
            return $b['priority'] - $a['priority'];
        });

        return $this->data;
    }
}