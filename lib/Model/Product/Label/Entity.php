<?php

namespace Model\Product\Label;

class Entity {
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

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
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
        if ($this->image) {
            $urls = \App::config()->productLabel['url'];

            return $urls[$size] . $this->image;
        } else {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function isSale()
    {
        return $this->id == self::LABEL_SALE;
    }
}
