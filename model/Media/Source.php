<?php
namespace Model\Media;

class Source {
    /** @var string */
    public $id = '';
    /** @var string */
    public $url = '';
    /** @var string */
    public $type = '';
    /** @var string */
    public $width = '';
    /** @var string */
    public $height = '';

    public function __construct(array $data = []) {
        if (isset($data['id'])) $this->id = (string)$data['id'];
        if (isset($data['url'])) $this->url = (string)$data['url'];
        if (isset($data['type'])) $this->type = (string)$data['type'];
        if (isset($data['width'])) $this->width = (string)$data['width'];
        if (isset($data['height'])) $this->height = (string)$data['height'];
    }
}