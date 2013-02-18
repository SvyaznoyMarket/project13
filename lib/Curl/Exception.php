<?php

namespace Curl;

class Exception extends \RuntimeException {
    private $content;

    /**
     * @param $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }
}