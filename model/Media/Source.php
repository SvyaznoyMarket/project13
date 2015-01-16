<?php
namespace Model\Media;

class Source {
    /** @var string|null */
    public $type;
    /** @var string|null */
    public $url;
    /** @var string|null */
    public $width;
    /** @var string|null */
    public $height;

    public function __construct($data = null) {
        if (isset($data['type'])) $this->type = $data['type'];
        if (isset($data['url'])) $this->url = $data['url'];
        if (isset($data['width'])) $this->width = $data['width'];
        if (isset($data['height'])) $this->height = $data['height'];
    }
}