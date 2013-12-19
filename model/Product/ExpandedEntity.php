<?php

namespace Model\Product;

class ExpandedEntity extends BasicEntity {
    /** @var int */
    protected $labelId;
    /** @var float */
    protected $rating;
    /** @var int */
    protected $ratingCount;
    /** @var Label\Entity|null */
    protected $label;
    /** @var string */
    protected $article;
    /** @var Property\Entity[] */
    protected $property = [];
    /** @var int */
    protected $priceAverage;
    /** @var int */
    protected $priceOld;
    /** @var Model\Entity */
    protected $model;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('status_id', $data)) $this->setStatusId($data['status_id']);
        if (array_key_exists('label_id', $data)) $this->setLabelId($data['label_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('article', $data)) $this->setArticle($data['article']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('rating', $data)) $this->setRating($data['rating']);
        if (array_key_exists('rating_count', $data)) $this->setRatingCount($data['rating_count']);
        if (array_key_exists('category', $data) && (bool)$data['category']) {
            $categoryData = reset($data['category']);
            $this->setMainCategory(new Category\Entity($categoryData));
        };
        if (array_key_exists('property', $data) && (bool)$data['property']) {
            usort($data['property'], function(array $a, array $b) {
                return $a['position'] - $b['position'];
            });

            foreach ($data['property'] as $propertyData) {
                if (!(bool)$propertyData['is_view_list']) continue;

                $property = new Property\Entity($propertyData);
                if (!$property->getStringValue()) continue;

                $this->addProperty($property);
            }
        }
        if (array_key_exists('label', $data)) {
            if (isset($data['label'][0]) && (bool)$data['label'][0]) {
                $this->setLabel(new Label\Entity($data['label'][0]));
            } elseif ((bool)$data['label']) {
                $this->setLabel(new Label\Entity($data['label']));
            }
        }
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('price_average', $data)) $this->setPriceAverage($data['price_average']);
        if (array_key_exists('price_old', $data)) $this->setPriceOld($data['price_old']);
        if (array_key_exists('state', $data) && (bool)$data['state']) $this->setState(new State\Entity($data['state']));
        if (array_key_exists('model', $data) && (bool)$data['model']) $this->setModel(new Model\Entity($data['model']));
        if (array_key_exists('stock', $data) && is_array($data['stock'])) $this->setStock(array_map(function($data) {
            return new Stock\Entity($data);
        }, $data['stock']));
        if (array_key_exists('ean', $data)) $this->setEan($data['ean']);

        $this->calculateState();
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
     * @param \Model\Product\Label\Entity|null $label
     */
    public function setLabel(Label\Entity $label = null) {
        $this->label = $label;
    }

    /**
     * @return \Model\Product\Label\Entity|null
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param int $labelId
     */
    public function setLabelId($labelId) {
        $this->labelId = $labelId ? (int)$labelId : null;
    }

    /**
     * @return int
     */
    public function getLabelId() {
        return $this->labelId;
    }

    /**
     * @param Model\Entity $model
     */
    public function setModel(Model\Entity $model = null) {
        $this->model = $model;
    }

    /**
     * @return Model\Entity
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * @param int $priceAverage
     */
    public function setPriceAverage($priceAverage) {
        $this->priceAverage = (int)$priceAverage;
    }

    /**
     * @return int
     */
    public function getPriceAverage() {
        return $this->priceAverage;
    }

    /**
     * @param int $priceOld
     */
    public function setPriceOld($priceOld) {
        $this->priceOld = (int)$priceOld;
    }

    /**
     * @return int
     */
    public function getPriceOld() {
        return $this->priceOld;
    }

    public function setProperty(array $properties) {
        $this->property = [];
        foreach ($properties as $property) {
            $this->addProperty($property);
        }
    }

    public function addProperty(Property\Entity $property) {
        $this->property[] = $property;
    }

    public function getProperty() {
        return $this->property;
    }

    /**
     * @param float $rating
     */
    public function setRating($rating) {
        $this->rating = (float)$rating;
    }

    /**
     * @return float
     */
    public function getRating() {
        return $this->rating;
    }

    /**
     * @param int $ratingCount
     */
    public function setRatingCount($ratingCount) {
        $this->ratingCount = (int)$ratingCount;
    }

    /**
     * @return int
     */
    public function getRatingCount() {
        return $this->ratingCount;
    }
}