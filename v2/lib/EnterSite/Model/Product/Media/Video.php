<?php

namespace EnterSite\Model\Product\Media;

use EnterSite\Model\ImportArrayConstructorTrait;

class Video {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $content;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('content', $data)) $this->content = (string)$data['content'];
    }
}