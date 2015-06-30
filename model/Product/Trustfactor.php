<?php
namespace Model\Product;

use Model\Media;

class Trustfactor {

    const UID_MNOGO_RU = '10259a2e-ce37-49a7-8971-8366de3337d3';
    const UID_SBERBANK_SPASIBO = 'ab3ca73c-6cc4-4820-b303-8165317420d5';
    const TAG_NEW_PRODUCT_CARD = 'product-2015';
    const TAG_NEW_PRODUCT_CARD_PARTNER = 'product-2015-partner';

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

    public function __construct(array $data = []) {
        if (isset($data['uid'])) $this->uid = $data['uid'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['type'])) $this->type = $data['type'];
        if (isset($data['link'])) $this->link = $data['link'];
        if (isset($data['alt'])) $this->alt = $data['alt'];
        if (isset($data['tags']) && is_array($data['tags'])) $this->tags = $data['tags'];
        if (isset($data['media']) && is_array($data['media'])) $this->media = new Media($data['media']);
    }

    /**
     * @param $tag string
     * @return bool
     */
    public function hasTag($tag) {
        return in_array($tag, $this->tags);
    }

    /** Возвращает URL оригинального изображения
     * @return null|string
     */
    public function getImage() {
        return $this->media ? $this->media->getOriginalImage() : null;
    }

}