<?php

namespace Model\Product\Label;

use Model\Media;

class Entity {
    use \Model\MediaHostTrait;

    // Распродажа
    const LABEL_SALE = 1;
    // Акция
    const LABEL_PROMO = 2;
    // Скидка
    const LABEL_DISCOUNT = 3;
    // Супер Цена
    const LABEL_SUPER_PRICE = 4;
    // Продукт Года
    const LABEL_YEAR_PRODUCT = 5;
    // Новинка
    const LABEL_NEW = 6;
    // Для болельщика
    const LABEL_FANS = 7;
    // WOW-Кредит
    const LABEL_CREDIT = 8;
    // Подарок
    const LABEL_GIFT = 9;

    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $image;
    /** @var Media[]  */
    private $medias;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('media_image', $data) && !empty($data['media_image'])) $this->setImage($data['media_image']);
        if (array_key_exists('medias', $data) && is_array($data['medias'])) {
            $this->medias = array_map(function($mediaData){ return new Media($mediaData); }, $data['medias']);
        }
    }

    public function setId($id) {
        $this->id = (int)$id;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = (string)$name;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @param string $image
     */
    public function setImage($image) {
        $this->image = (string)$image;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param int $size
     * @return null|string
     */
    public function getImageUrl($size = 0) {
        $tag = $size == 0 ? '66x23' : '124x38';
        if ($this->image) {
            $urls = \App::config()->productLabel['url'];
            return $this->getHost() . $urls[$size] . $this->image;
        } elseif ($this->medias) {
            foreach ($this->medias as $media) {
                if (in_array($tag, $media->tags)) return isset($media->sources[0]) ? $media->sources[0]->url : null;
            }
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isSale()
    {
        return $this->id == self::LABEL_SALE;
    }
}
