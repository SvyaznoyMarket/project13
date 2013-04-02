<?php

namespace Model;

trait MediaHostTrait {
    /**
     * @return string
     */
    public function getHost() {
        static $hosts;

        if (!$hosts) $hosts = \App::config()->mediaHost;

        $index = !empty($this->id) ? ($this->id % count($hosts)) : rand(0, count($hosts) - 1);

        return isset($hosts[$index]) ? $hosts[$index] : '';
    }
}