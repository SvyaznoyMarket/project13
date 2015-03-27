<?php

namespace Model\Product;

class Entity extends BasicEntity {

    /** Дефолтное отображение связанных товаров - аксессуары сверху, смежные товары в футере */
    const DEFAULT_CONNECTED_PRODUCTS_VIEW_MODE = 1;

    /** @var int */
    protected $viewId;
    /** @var int */
    protected $typeId;
    /** @var int */
    protected $setId;
    /** @var int */
    protected $labelId;
    /** @var bool */
    protected $isModel;
    /** @var bool */
    protected $isPrimaryLine;
    /** @var int */
    protected $modelId;
    /** @var int|null */
    protected $score;
    /** @var string */
    protected $webName;
    /** @var string */
    protected $prefix;
    /** @var string */
    protected $barcode;
    /** @var string */
    protected $tagline;
    /** @var string */
    protected $announce;
    /** @var string */
    protected $description;
    /** @var float */
    protected $rating;
    /** @var int */
    protected $ratingCount;
    /** @var Category\Entity[] */
    protected $category = [];
    /** @var Property\Group\Entity[] */
    protected $propertyGroup = [];
    /** @var Property\Entity[] */
    protected $property = [];
    /** @var Property\Entity[] */
    protected $mainProperties = [];
    /** @var [] */
    protected $secondaryGroupedProperties = [];
    /** @var \Model\Tag\Entity[] */
    protected $tag = [];
    /** @var Media\Entity[] */
    protected $photo = [];
    /** @var \Model\Brand\Entity|null */
    protected $brand;
    /** @var Label\Entity|null */
    protected $label;
    /** @var Type\Entity|null */
    protected $type;
    /** @var int */
    protected $commentCount;
    /** @var float */
    protected $priceAverage;
    /** @var float */
    protected $priceOld;
    /** @var Service\Entity[] */
    protected $service = [];
    /** @var [] */
    protected $groupedProperties = [];
    /** @var int */
    protected $connectedProductsViewMode;
    /** @var array */
    protected $accessoryId = [];
    /** @var array */
    protected $relatedId = [];
    /** @var Warranty\Entity[] */
    protected $warranty = [];
    /** @var \Model\Region\Entity */
    protected $nearestCity = [];

    public function __construct(array $data = []) {
        parent::__construct($data);

        if (array_key_exists('view_id', $data)) $this->setViewId($data['view_id']);
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('set_id', $data)) $this->setSetId($data['set_id']);
        if (array_key_exists('label_id', $data)) $this->setLabelId($data['label_id']);
        if (array_key_exists('is_model', $data)) $this->setIsModel($data['is_model']);
        if (array_key_exists('is_primary_line', $data)) $this->setIsPrimaryLine($data['is_primary_line']);
        if (array_key_exists('model_id', $data)) $this->setModelId($data['model_id']);
        if (array_key_exists('score', $data)) $this->setScore($data['score']);
        if (array_key_exists('name_web', $data)) $this->setWebName($data['name_web']);
        if (array_key_exists('prefix', $data)) $this->setPrefix($data['prefix']);
        if (array_key_exists('tagline', $data)) $this->setTagline($data['tagline']);
        if (array_key_exists('announce', $data)) $this->setAnnounce($data['announce']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('rating', $data)) $this->setRating($data['rating']);
        if (array_key_exists('rating_count', $data)) $this->setRatingCount($data['rating_count']);
        if (array_key_exists('category', $data) && is_array($data['category'])) {
            foreach ($data['category'] as $categoryData) {
                $this->addCategory(new Category\Entity($categoryData));
            }
        }
        if (array_key_exists('connected_products_view_mode', $data)) $this->setConnectedProductsViewMode($data['connected_products_view_mode']);
        if (array_key_exists('property_group', $data) && is_array($data['property_group'])) $this->setPropertyGroup(array_map(function($data) {
            return new Property\Group\Entity($data);
        }, $data['property_group']));
        if (array_key_exists('property', $data) && is_array($data['property'])) foreach ($data['property'] as $property) {
            $this->addProperty(new Property\Entity($property));
        }
        if (array_key_exists('tag', $data) && is_array($data['tag'])) $this->setTag(array_map(function($data) {
            return new \Model\Tag\Entity($data);
        }, $data['tag']));

        if (array_key_exists('media', $data) && is_array($data['media'])) {
            foreach ($data['media'] as $media) {
                $media = new Media\Entity($media);
                if ($media->getTypeId() == 1) {
                    $this->photo[] = $media;
                }
            }
        }

        if (array_key_exists('brand', $data) && (bool)$data['brand']) $this->setBrand(new \Model\Brand\Entity($data['brand']));
        if (array_key_exists('label', $data)) {
            if (isset($data['label'][0]) && (bool)$data['label'][0]) {
                $this->setLabel(new Label\Entity($data['label'][0]));
            } elseif ((bool)$data['label']) {
                $this->setLabel(new Label\Entity($data['label']));
            }
        }
        if (array_key_exists('type', $data) && (bool)$data['type']) $this->setType(new Type\Entity($data['type']));
        if (array_key_exists('comment_count', $data)) $this->setCommentCount($data['comment_count']);
        if (array_key_exists('price_average', $data)) $this->setPriceAverage($data['price_average']);
        if (array_key_exists('price_old', $data)) $this->setPriceOld($data['price_old']);
        if (array_key_exists('service', $data) && is_array($data['service'])) $this->setService(array_map(function($data) {
            return new Service\Entity($data);
        }, $data['service']));
        if (array_key_exists('related', $data)) $this->setRelatedId($data['related']);
        if (array_key_exists('accessories', $data)) $this->setAccessoryId($data['accessories']);
        if (array_key_exists('warranty', $data) && is_array($data['warranty'])) foreach ($data['warranty'] as $warranty) {
            $this->addWarranty(new Warranty\Entity($warranty));
        }
        if (array_key_exists('nearest_city', $data) && is_array($data['nearest_city'])) foreach ($data['nearest_city'] as $city) {
            $this->addNearestCity(new \Model\Region\Entity($city));
        }

        $indexedPropertyGroups = [];
        foreach ($this->propertyGroup as $group) {
            if (!isset($this->groupedProperties[$group->getId()])) {
                $this->groupedProperties[$group->getId()] = ['group' => $group, 'properties' => []];
            }

            $indexedPropertyGroups[$group->getId()] = $group;
        }

        foreach ($this->property as $property) {
            if (isset($this->groupedProperties[$property->getGroupId()])) {
                $this->groupedProperties[$property->getGroupId()]['properties'][] = $property;
            }
        }

        $propertiesCount = $this->getPropertiesCount();
        if (((!$this->getTagline() && !count($this->getModel()) && !$this->getDescription() && $propertiesCount < 16) || $propertiesCount < 8) && !$this->hasLongProperties()) {
            foreach ($this->groupedProperties as $group) {
                if (!(bool)$group['properties']) continue;

                foreach ($group['properties'] as $property) {
                    $this->mainProperties[] = $property;
                }
            }
        } else {
            foreach ($this->property as $property) {
                /** @var \Model\Product\Property\Entity $property */
                $stringValue = $property->getStringValue();

                if (!$property->getIsInList()) continue;
                if (!$stringValue || mb_strlen($stringValue) > 45) continue;

                $this->mainProperties[] = $property;
            }

            usort($this->mainProperties, function(Property\Entity $a, Property\Entity $b) {
                return $a->getPosition() - $b->getPosition();
            });

            $this->secondaryGroupedProperties = $this->groupedProperties;
        }

        // TODO удалить
        if ($this->isGifteryCertificate()) $this->state->setIsBuyable(true);
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
     * @param Category\Entity[] $categories
     * @return void
     */
    public function setCategory(array $categories) {
        $this->category = [];
        foreach ($categories as $category) {
            $this->addCategory($category);
        }
    }

    /**
     * @param Category\Entity $category
     */
    public function addCategory(Category\Entity $category) {
        $this->category[] = $category;
    }

    /**
     * @return Category\Entity[]
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @return Category\Entity|null
     */
    public function getLastCategory() {
        $lastCategory = end($this->category);
        reset($this->category);
        return $lastCategory;
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
    public function getLink($withSuffix = true, $region = NULL) {
        return $this->link;
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
     * @param string $announce
     */
    public function setAnnounce($announce) {
        $this->announce = (string)$announce;
    }

    /**
     * @return string
     */
    public function getAnnounce() {
        return $this->announce;
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

    public function setBrand(\Model\Brand\Entity $brand = null) {
        $this->brand = $brand;
    }

    public function getBrand() {
        return $this->brand;
    }

    /**
     * @param int $commentCount
     */
    public function setCommentCount($commentCount) {
        $this->commentCount = (int)$commentCount;
    }

    /**
     * @return int
     */
    public function getCommentCount() {
        return $this->commentCount;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = (string)$description;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param boolean $isModel
     */
    public function setIsModel($isModel) {
        $this->isModel = (bool)$isModel;
    }

    /**
     * @return boolean
     */
    public function getIsModel() {
        return $this->isModel;
    }

    /**
     * @param boolean $isPrimaryLine
     */
    public function setIsPrimaryLine($isPrimaryLine) {
        $this->isPrimaryLine = (bool)$isPrimaryLine;
    }

    /**
     * @return boolean
     */
    public function getIsPrimaryLine() {
        return $this->isPrimaryLine;
    }

    /**
     * @param int|null $labelId
     */
    public function setLabelId($labelId = null) {
        $this->labelId = $labelId ? (int)$labelId : null;
    }

    /**
     * @return int
     */
    public function getLabelId() {
        return $this->labelId;
    }

    public function getPhoto() {
        return $this->photo;
    }

    /**
     * @param int $modelId
     */
    public function setModelId($modelId = null) {
        $this->modelId = $modelId ? (int)$modelId : null;
    }

    /**
     * @return int|null
     */
    public function getModelId() {
        return $this->modelId;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix) {
        $this->prefix = (string)$prefix;
    }

    /**
     * @return string
     */
    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * @param float $priceAverage
     */
    public function setPriceAverage($priceAverage) {
        $this->priceAverage = (float)$priceAverage;
    }

    /**
     * @return float
     */
    public function getPriceAverage() {
        return $this->priceAverage;
    }

    /**
     * @param float $priceOld
     */
    public function setPriceOld($priceOld) {
        $this->priceOld = (float)$priceOld;
    }

    /**
     * @return float
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
        $this->property[$property->getId()] = $property;
    }

    public function getProperty() {
        return array_values($this->property);
    }

    public function getMainProperties() {
        return $this->mainProperties;
    }

    public function getSecondaryGroupedProperties() {
        return $this->secondaryGroupedProperties;
    }

    /**
     * @param int $id
     * @return Property\Entity|null
     */
    public function getPropertyById($id) {
        return isset($this->property[$id]) ? $this->property[$id] : null;
    }

    public function setPropertyGroup(array $propertyGroups) {
        $this->propertyGroup = [];
        foreach ($propertyGroups as $propertyGroup) {
            $this->addPropertyGroup($propertyGroup);
        }
    }

    public function addPropertyGroup(Property\Group\Entity $propertyGroup) {
        $this->propertyGroup[] = $propertyGroup;
    }

    public function getPropertyGroup() {
        return $this->propertyGroup;
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

    /**
     * @param int|null $score
     */
    public function setScore($score) {
        $this->score = (int)$score;
    }

    /**
     * @return int|null
     */
    public function getScore() {
        return $this->score;
    }

    /**
     * @param int $setId
     */
    public function setSetId($setId) {
        $this->setId = (int)$setId;
    }

    /**
     * @return int
     */
    public function getSetId() {
        return $this->setId;
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
     * @param Stock\Entity[] $stocks
     */
    public function setStock(array $stocks) {
        $this->stock = [];
        foreach ($stocks as $stock) {
            $this->addStock($stock);
        }
    }

    /**
     * @param Stock\Entity $stock
     */
    public function addStock(Stock\Entity $stock) {
        $this->stock[] = $stock;
    }

    /**
     * @return array|Stock\Entity[]
     */
    public function getStock() {
        return $this->stock;
    }

    public function setTag(array $tags) {
        $this->tag = [];
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }
    }

    public function addTag(\Model\Tag\Entity $tag) {
        $this->tag[] = $tag;
    }

    public function getTag() {
        return $this->tag;
    }

    /**
     * @param string $tagline
     */
    public function setTagline($tagline) {
        $this->tagline = (string)$tagline;
    }

    /**
     * @return string
     */
    public function getTagline() {
        return $this->tagline;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId) {
        $this->typeId = (int)$typeId;
    }

    /**
     * @return int
     */
    public function getTypeId() {
        return $this->typeId;
    }

    /**
     * @param int $viewId
     */
    public function setViewId($viewId) {
        $this->viewId = (int)$viewId;
    }

    /**
     * @return int
     */
    public function getViewId() {
        return $this->viewId;
    }

    /**
     * @param string $webName
     */
    public function setWebName($webName) {
        $this->webName = (string)$webName;
    }

    /**
     * @return string
     */
    public function getWebName() {
        return $this->webName;
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
     * @param \Model\Product\Type\Entity|null $type
     */
    public function setType(Type\Entity $type = null) {
        $this->type = $type;
    }

    /**
     * @return \Model\Product\Type\Entity|null
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param Service\Entity[] $services
     */
    public function setService(array $services) {
        $this->service = [];
        foreach ($services as $service) {
            $this->addService($service);
        }
    }

    /**
     * @param Service\Entity $service
     */
    public function addService(Service\Entity $service) {
        $this->service[] = $service;
    }

    /**
     * @return Service\Entity[]
     */
    public function getService() {
        return $this->service;
    }


    /**
     * @return bool
     */
    public function hasSaleLabel()
    {
        if ($this->label) {
            return $this->label->isSale();
        }

        return false;
    }

    /**
     * @return array
     */
    public function getGroupedProperties()
    {
        return $this->groupedProperties;
    }

    public function getPropertiesCount()
    {
        //$countProperties = count($product->getProperty());
        //$countProperties = count($product->getGroupedProperties());
        $count = 0;

        //foreach ($product->getProperty() as $property) if ( $property->getValue() ) $countProperties++;
        foreach ($this->groupedProperties as $group) {
            if (!(bool)$group['properties']) continue;
            foreach ($group['properties'] as $property) {
                $count++;
            }
        }

        return $count;
    }

    public function hasLongProperties()
    {
        foreach ($this->groupedProperties as $group) {
            if (!(bool)$group['properties']) continue;
            foreach ($group['properties'] as $property) {
                /* @var $property Property\Entity */
                if (mb_strlen($property->getStringValue()) > 45) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param int $connectedProductsViewMode
     */
    public function setConnectedProductsViewMode($connectedProductsViewMode = null)
    {
        $this->connectedProductsViewMode = $connectedProductsViewMode ? (int)$connectedProductsViewMode : null;
    }

    /**
     * @return int
     */
    public function getConnectedProductsViewMode()
    {
        return $this->connectedProductsViewMode;
    }

    /**
     * @param array $accessoryId
     */
    public function setAccessoryId($accessoryId)
    {
        $this->accessoryId = $accessoryId;
    }

    /**
     * @return array
     */
    public function getAccessoryId()
    {
        return $this->accessoryId;
    }

    /**
     * @param $relatedId
     * @return void
     */
    public function setRelatedId($relatedId)
    {
        $this->relatedId = $relatedId;
    }

    /**
     * @return array
     */
    public function getRelatedId()
    {
        return $this->relatedId;
    }

    /**
     * @param array
     */
    public function setWarranty($warranties)
    {
        $this->warranty = [];
        foreach ($warranties as $warranty) {
            $this->addWarranty($warranty);
        }
    }

    /**
     * @return array|Warranty\Entity[]
     */
    public function getWarranty()
    {
        return $this->warranty;
    }

    public function addWarranty(Warranty\Entity $warranty) {
        $this->warranty[] = $warranty;
    }


    /**
     * @param \Model\Region\Entity[] $nearestCity
     */
    public function setNearestCity($nearestCity)
    {
        $this->nearestCity = [];
        foreach ($nearestCity as $city) {
            $this->addNearestCity($city);
        }
    }

    /**
     * @return \Model\Region\Entity[]
     */
    public function getNearestCity()
    {
        return $this->nearestCity;
    }

    public function addNearestCity(\Model\Region\Entity $city) {
        $this->nearestCity[] = $city;
    }


}