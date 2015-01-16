<?php
namespace Model\Product;

use Model\Media;

class Trustfactor {
    /** @var string|null */
    public $uid;
    /** @var string|null */
    public $name;
    /** @var string|null */
    public $type;
    /** @var string|null */
    public $link;
    /** @var string|null */
    public $alt;
    /** @var string[] */
    public $tags = [];
    /** @var Media|null */
    public $media;

    public function __construct($data = null) {
        if (isset($data['uid'])) $this->uid = $data['uid'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['type'])) $this->type = $data['type'];
        if (isset($data['link'])) $this->link = $data['link'];
        if (isset($data['alt'])) $this->alt = $data['alt'];
        if (isset($data['tags']) && is_array($data['tags'])) $this->tags = $data['tags'];
        if (isset($data['media'])) $this->media = new Media($data['media']);
    }
}