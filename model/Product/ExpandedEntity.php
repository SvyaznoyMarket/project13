<?php

namespace Model\Product;

class ExpandedEntity extends BasicEntity {
    /** @var int|null */
    protected $labelId;
    /** @var float|null */
    protected $rating;
    /** @var int|null */
    protected $ratingCount;
    /** @var Label\Entity|null */
    protected $label;
    /** @var Property\Entity[] */
    protected $property = [];
    /** @var int|null */
    protected $priceAverage;
    /** @var int|null */
    protected $priceOld;

    public function __construct(array $data = []) {
        parent::__construct($data);

        if (isset($data['label_id'])) $this->setLabelId($data['label_id']);
        if (isset($data['rating'])) $this->setRating($data['rating']);
        if (isset($data['rating_count'])) $this->setRatingCount($data['rating_count']);
        if (isset($data['property']) && (bool)$data['property']) {
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
        if (isset($data['label'])) {
            if (isset($data['label'][0]) && (bool)$data['label'][0]) {
                $this->setLabel(new Label\Entity($data['label'][0]));
            } elseif ((bool)$data['label']) {
                $this->setLabel(new Label\Entity($data['label']));
            }
        }
        if (isset($data['price_average'])) $this->setPriceAverage($data['price_average']);
        if (isset($data['price_old'])) $this->setPriceOld($data['price_old']);
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