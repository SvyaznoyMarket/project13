<?php

namespace Model\Product;

class BasicEntity {
    use \Model\MediaHostTrait;

    /** @var int */
    protected $id;
    /** @var string */
    protected $barcode;
    /** @var string */
    protected $article;
    /** @var string */
    protected $name;
    /** @var string */
    protected $link;
    /** @var string */
    protected $token;
    /** @var string */
    protected $image;
    /** @var int */
    protected $price;
    /** @var State\Entity */
    protected $state;
    /** @var Line\Entity */
    protected $line;
    /** @var Category\Entity */
    protected $mainCategory;
    /** @var Category\Entity */
    protected $parentCategory;
    /** @var Stock\Entity[] */
    protected $stock = [];
    /** @var array */
    protected $ean;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('article', $data)) $this->setArticle($data['article']);
        if (array_key_exists('bar_code', $data)) $this->setBarcode($data['bar_code']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('category', $data) && (bool)$data['category']) {
            $categoryData = reset($data['category']);
            if ((bool)$categoryData) $this->setMainCategory(new Category\Entity($categoryData));

            $categoryData = end($data['category']);
            if ((bool)$categoryData) $this->setParentCategory(new Category\Entity($categoryData));
        };
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('state', $data) && (bool)$data['state']) $this->setState(new State\Entity($data['state']));
        if (array_key_exists('line', $data) && (bool)$data['line']) $this->setLine(new Line\Entity($data['line']));
        if (array_key_exists('stock', $data) && is_array($data['stock'])) $this->setStock(array_map(function($data) {
            return new Stock\Entity($data);
        }, $data['stock']));
        if (array_key_exists('ean', $data)) $this->setEan($data['ean']);
    }

    /**
     * @param string $article
     */
    public function setArticle($article) {
        $this->article = (string)$article;
    }

    /**
     * @return string
     */
    public function getArticle() {
        return $this->article;
    }

    /**
     * @param string $barcode
     */
    public function setBarcode($barcode) {
        $this->barcode = (string)$barcode;
    }

    /**
     * @return string
     */
    public function getBarcode() {
        return $this->barcode;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (string)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
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
     * @param string $link
     */
    public function setLink($link) {
        $this->link = (string)$link;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param int $price
     */
    public function setPrice($price) {
        $this->price = (int)$price;
    }

    /**
     * @return int
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param State\Entity $state
     */
    public function setState(State\Entity $state = null) {
        $this->state = $state;
    }

    /**
     * @return State\Entity
     */
    public function getState() {
        return $this->state;
    }

    /**
     * @param string $token
     */
    public function setToken($token) {
        $this->token = (string)$token;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param int $size
     * @return null|string
     */
    public function getImageUrl($size = 1) {
        if ($this->image) {
            $urls = \App::config()->productPhoto['url'];

            return $this->getHost() . $urls[$size] . $this->image;
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getNameWithCategory() {
        $name = $this->name;

        if ($this->mainCategory) {
            $name .= ' - ' . $this->mainCategory->getName();
        }

        return $name;
    }

    /**
     * @param \Model\Product\Category\Entity $mainCategory
     */
    public function setMainCategory(Category\Entity $mainCategory = null) {
        $this->mainCategory = $mainCategory;
    }

    /**
     * @return \Model\Product\Category\Entity
     */
    public function getMainCategory() {
        return $this->mainCategory;
    }

    /**
     * @return bool
     */
    public function getIsBuyable() {
        return $this->getState() && $this->getState()->getIsBuyable() && $this->getState()->getIsStore();
    }

    /**
     * @param \Model\Product\Line\Entity $line|null
     */
    public function setLine(Line\Entity $line = null) {
        $this->line = $line;
    }

    /**
     * @return Line\Entity
     */
    public function getLine() {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getPath() {
        return trim(preg_replace('/^\/product\//' , '', $this->link), '/');
    }

    public function setStock(array $stocks) {
        $this->stock = [];
        foreach ($stocks as $stock) {
            $this->addStock($stock);
        }
    }

    public function addStock(Stock\Entity $stock) {
        $this->stock[] = $stock;
    }

    public function getStock() {
        return $this->stock;
    }

    /**
     * @param \Model\Product\Category\Entity $parentCategory
     */
    public function setParentCategory(\Model\Product\Category\Entity $parentCategory) {
        $this->parentCategory = $parentCategory;
    }

    /**
     * @return \Model\Product\Category\Entity
     */
    public function getParentCategory() {
        return $this->parentCategory;
    }

    public function setEan($ean)
    {
        $this->ean = $ean;
    }

    public function getEan()
    {
        return $this->ean;
    }

}