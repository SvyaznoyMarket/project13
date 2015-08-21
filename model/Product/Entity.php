<?php

namespace Model\Product;

use Model\Media;
use Model\Product\Delivery\ProductDelivery;
use Model\EnterprizeCoupon\Entity as Coupon;

class Entity {
    use \Model\MediaHostTrait;

    const LABEL_ID_PODARI_ZHIZN = 17;
    const PARTNER_OFFER_TYPE_SLOT = 2;
    /** Электронный подарочный сертификат giftery.ru */
    const GIFTERY_UID = '684fb825-ebf5-4e4f-be2b-96a81e938cb2';

    /** @var string|null */
    public $ui;
    /** @var int|null */
    public $id;
    /** @var string|null */
    public $barcode;
    /** @var string|null */
    protected $article;
    /** @var string|null */
    protected $name;
    /** @var string|null */
    protected $link;
    /** @var string|null */
    protected $token;
    /** @var float|null */
    protected $price;
    /** @var State\Entity|null */
    protected $state;
    /** @var int|null */
    protected $statusId;
    /** @var Kit\Entity[] */
    protected $kit = [];
    /** @var bool */
    protected $isKitLocked = false;
    /**
     * Все категории, к которым привязан товар
     * @var Category\Entity[]
     */
    public $categories = [];
    /**
     * @deprecated
     * @var Category\Entity[]
     */
    protected $category = [];
    /** @var Category\Entity */
    protected $rootCategory;
    /** @var Category\Entity */
    protected $parentCategory;
    /** @var Stock\Entity[] */
    protected $stock = [];
    /** $var array */
    protected $partnersOffer = [];
    /** @var array */
    protected $ean = [];
    /** @var float|null */
    protected $avgScore;
    /** @var float|null */
    protected $avgStarScore;
    /** @var int|null */
    protected $numReviews;
    /** @var bool|null */
    protected $isInShowroomsOnly;
    /** @var bool|null */
    protected $isInShopsOnly;
    /** @var bool|null */
    protected $isOnlyFromPartner;
    /** @var bool|null */
    protected $hasPartnerStock;
    /** @var bool|null */
    protected $isUpsale = false;
    /** @var Model\Entity|null */
    protected $model;
    /** @var string|null */
    protected $seoTitle;
    /** @var string|null */
    protected $seoKeywords;
    /** @var string|null */
    protected $seoDescription;
    /** @var int */
    protected $viewId;
    /** @var int */
    protected $typeId;
    /** @var int */
    protected $setId;
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
    protected $tagline;
    /** @var string */
    protected $announce;
    /** @var string */
    protected $description;
    /** @var float */
    protected $rating;
    /** @var int */
    protected $ratingCount;
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
    /** @var \Model\Brand\Entity|null */
    protected $brand;
    /** @var Label|null */
    protected $label;
    /** @var Type\Entity|null */
    protected $type;
    /** @var float */
    protected $priceAverage;
    /** @var float */
    protected $priceOld;
    /** @var [] */
    protected $groupedProperties = [];
    /** @var array */
    protected $accessoryId = [];
    /** @var array */
    protected $relatedId = [];
    /** @var \Model\Region\Entity */
    protected $nearestCity = [];
    /** @var ProductDelivery|null */
    public $delivery;
    /** @var \Model\Media[] */
    public $medias = [];
    /** @var array */
    public $json3d = [];
    /** @var Coupon[] */
    public $coupons = [];

    public function __construct($data = []) {
        $this->importFromCore($data);
    }

    public function importFromCore($data = []) {
        $templateHelper = new \Helper\TemplateHelper();

        if (isset($data['id'])) $this->setId($data['id']);
        else if (isset($data['core_id'])) $this->setId($data['core_id']);
        if (isset($data['ui'])) $this->setUi($data['ui']);
        if (isset($data['uid'])) $this->setUi($data['uid']); // для scms
        if (isset($data['status_id'])) $this->setStatusId($data['status_id']);
        if (isset($data['link'])) $this->setLink($data['link']);
        if (isset($data['url'])) $this->setLink($data['url']);
        if (isset($data['token'])) $this->setToken($data['token']);
        if (isset($data['article'])) $this->setArticle($data['article']);
        if (isset($data['bar_code'])) $this->setBarcode($data['bar_code']);
        if (isset($data['price'])) $this->setPrice($data['price']);
        if (isset($data['state']) && (bool)$data['state']) $this->setState(new State\Entity($data['state']));
        if (isset($data['stock']) && is_array($data['stock'])) $this->setStock(array_map(function($data) {
            return new Stock\Entity($data);
        }, $data['stock']));
        if (isset($data['partners_offer'])) $this->setPartnersOffer(array_map(function($v) { return $v; }, $data['partners_offer']));
        if (isset($data['ean'])) $this->setEan($data['ean']);
        if (isset($data['avg_score'])) $this->setAvgScore($data['avg_score']);
        if (isset($data['avg_star_score'])) $this->setAvgStarScore($data['avg_star_score']);
        if (isset($data['num_reviews'])) $this->setNumReviews($data['num_reviews']);
        if (isset($data['is_upsale'])) $this->setIsUpsale($data['is_upsale']);
        if (isset($data['model']) && $data['model']) $this->setModel(new Model\Entity($data['model']));

        if (array_key_exists('kit', $data) && is_array($data['kit'])) $this->setKit(array_map(function($data) {
            return new Kit\Entity($data);
        }, $data['kit']));
        if (array_key_exists('is_kit_locked', $data)) $this->setIsKitLocked($data['is_kit_locked']);

        $this->calculateState($data);

        if (array_key_exists('view_id', $data)) $this->setViewId($data['view_id']);
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('set_id', $data)) $this->setSetId($data['set_id']);
        if (array_key_exists('is_model', $data)) $this->setIsModel($data['is_model']);
        if (array_key_exists('is_primary_line', $data)) $this->setIsPrimaryLine($data['is_primary_line']);
        if (array_key_exists('model_id', $data)) $this->setModelId($data['model_id']);
        if (array_key_exists('score', $data)) $this->setScore($data['score']);
        if (isset($data['name'])) $this->setName($templateHelper->unescape($data['name'])); // Редакция в 1С не использует HTML сущности и теги в данном поле
        if (array_key_exists('name_web', $data)) $this->setWebName($templateHelper->unescape($data['name_web'])); // Редакция в 1С не использует HTML сущности и теги в данном поле
        if (array_key_exists('prefix', $data)) $this->setPrefix($templateHelper->unescape($data['prefix'])); // Редакция в 1С не использует HTML сущности и теги в данном поле
        if (array_key_exists('tagline', $data)) $this->setTagline($data['tagline']);
        if (array_key_exists('announce', $data)) $this->setAnnounce($data['announce']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('rating', $data)) $this->setRating($data['rating']);
        if (array_key_exists('rating_count', $data)) $this->setRatingCount($data['rating_count']);
        if (array_key_exists('type', $data) && (bool)$data['type']) $this->setType(new Type\Entity($data['type']));
        if (array_key_exists('price_average', $data)) $this->setPriceAverage($data['price_average']);
        if (array_key_exists('price_old', $data)) $this->setPriceOld($data['price_old']);
        if (array_key_exists('related', $data)) $this->setRelatedId($data['related']);
        if (array_key_exists('accessories', $data)) $this->setAccessoryId($data['accessories']);
        if (array_key_exists('nearest_city', $data) && is_array($data['nearest_city'])) foreach ($data['nearest_city'] as $city) {
            $this->addNearestCity(new \Model\Region\Entity($city));
        }

        if (array_key_exists('medias', $data) && is_array($data['medias'])) $this->medias = array_map(function($mediaData) {return new Media($mediaData);}, $data['medias']);

        // TODO удалить
        if ($this->isGifteryCertificate()) $this->state->setIsBuyable(true);
    }

    public function importFromScms($data = []) {
        if (!empty($data['medias']) && is_array($data['medias'])) {
            foreach ($data['medias'] as $media) {
                if (is_array($media)) {
                    $this->medias[] = new \Model\Media($media);
                }
            }
        }

        if (!empty($data['json3d']) && is_array($data['json3d'])) {
            $this->json3d = $data['json3d'];
        }

        if (!empty($data['label']['uid'])) {
            $this->setLabel(new Label($data['label']));
        }

        if (!empty($data['brand']) && @$data['brand']['slug'] === 'tchibo-3569') {
            $this->setBrand(new \Model\Brand\Entity([
                'ui'        => @$data['brand']['uid'],
                'id'        => @$data['brand']['core_id'],
                'token'     => @$data['brand']['slug'],
                'name'      => @$data['brand']['name'],
                'media_image' => 'http://content.enter.ru/wp-content/uploads/2014/05/tchibo.png', // TODO после решения FCMS-740 заменить на URL из scms и удалить условие "@$thisData['brand']['slug'] === 'tchibo-3569'"
            ]));
        }

        if (!empty($data['tags'])) {
            $this->setTag(array_map(function($data) { return new \Model\Tag\Entity($data); }, $data['tags']));
        }

        // TODO отрефакторить, перенеся часть методов в модель категории
        if (!empty($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $category) {
                $this->categories[] = new \Model\Product\Category\Entity($category);
                if ($category['main']) {
                    $this->parentCategory = new \Model\Product\Category\Entity($category);
                    $this->category[] = new \Model\Product\Category\Entity($category);

                    while (!empty($category['parent'])) {
                        $category = $category['parent'];
                        $this->category[] = new \Model\Product\Category\Entity($category);
                    }

                    $this->category = array_reverse($this->category);
                    $this->rootCategory = new \Model\Product\Category\Entity($category);
                }
            }
        }

        if (isset($data['category']) && is_array($data['category'])) {
            foreach ($data['category'] as $categoryData) {
                // в ядре: 0 - root, last - parent
                $this->category[] = new Category\Entity($categoryData);
            }
        };


        $templateHelper = new \Helper\TemplateHelper();

        if (isset($data['title'])) {
            $this->setSeoTitle($templateHelper->unescape($data['title']));
        }

        if (isset($data['meta_keywords'])) {
            $this->setSeoKeywords($templateHelper->unescape($data['meta_keywords']));
        }

        if (isset($data['meta_description'])) {
            $this->setSeoDescription($templateHelper->unescape($data['meta_description']));
        }

        if (!empty($data['properties']) && is_array($data['properties'])) {
            $this->setProperty(array_map(function($data) { return new \Model\Product\Property\Entity($data); }, $data['properties']));
        }

        if (!empty($data['property_groups']) && is_array($data['property_groups'])) {
            $this->setPropertyGroup(array_map(function($data) { return new \Model\Product\Property\Group\Entity($data); }, $data['property_groups']));
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
        if (((!$this->getTagline() && !$this->getModel() && !$this->getDescription() && $propertiesCount < 16) || $propertiesCount < 8) && !$this->hasLongProperties()) {
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
     * @return Category\Entity[]
     */
    public function getCategory() {
        return $this->category;
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

    /** Краткое описание товара
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

    /**
     * @return Property\Entity[]
     */
    public function getProperty() {
        return array_values($this->property);
    }

    public function getMainProperties() {
        return $this->mainProperties;
    }

    public function getSecondaryGroupedProperties($excludePropertyNames = []) {
        if ($excludePropertyNames) {
            foreach ($excludePropertyNames as $key => $value) {
                $excludePropertyNames[$key] = mb_strtolower($value);
            }

            $secondaryGroupedProperties = [];
            foreach ($this->secondaryGroupedProperties as $key => $value) {
                foreach ($value['properties'] as $key2 => $property) {
                    /** @var Property\Entity $property */
                    if (in_array(mb_strtolower($property->getName()), $excludePropertyNames, true)) {
                        unset($value['properties'][$key2]);
                    }
                }

                $value['properties'] = array_values($value['properties']);

                $secondaryGroupedProperties[$key] = $value;
            }

            return $secondaryGroupedProperties;
        } else {
            return $this->secondaryGroupedProperties;
        }
    }

    public function getEquipmentProperty() {
        foreach ($this->property as $property) {
            if (mb_strtolower($property->getName()) === 'комплектация') {
                return $property;
            }
        }
        return null;
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
     * @return State\Entity|null
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
     * @return Stock\Entity[]
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
     * @param Label|null $label
     */
    public function setLabel(Label $label = null) {
        $this->label = $label;
    }

    /**
     * @return Label|null
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
     * @return array
     */
    public function getGroupedProperties() {
        return $this->groupedProperties;
    }

    public function getPropertiesCount() {
        $count = 0;
        foreach ($this->groupedProperties as $group) {
            $count += count($group['properties']);
        }

        return $count;
    }

    public function hasLongProperties() {
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

    /** Возвращает N свойств с in_view_list = true
     * @param $count
     * @return Property\Entity[]
     */
    public function getPropertiesInView($count) {
        $propertiesInView = array_filter($this->property, function(Property\Entity $entity){ return $entity->getIsInList(); });
        
        if (count($propertiesInView) < $count) {
            $propertiesInView = $this->property;
        }
        
        usort($propertiesInView, function(Property\Entity $a, Property\Entity $b) { return $a->getPosition() > $b->getPosition(); });
        $slicedProperties = array_slice($propertiesInView, 0, $count);
        // проставляем значения свойств
        foreach ($slicedProperties as $prop) {
            /** @var $prop Property\Entity */
            if (!$prop->getValue() && $prop->getOption()) $prop->setValue($prop->getOption()[0]->getValue());
        }
        return $slicedProperties;
    }

    /**
     * @param array $accessoryId
     */
    public function setAccessoryId($accessoryId) {
        $this->accessoryId = $accessoryId;
    }

    /**
     * @return array
     */
    public function getAccessoryId() {
        return $this->accessoryId;
    }

    /**
     * @param $relatedId
     * @return void
     */
    public function setRelatedId($relatedId) {
        $this->relatedId = $relatedId;
    }

    /**
     * @return array
     */
    public function getRelatedId() {
        return $this->relatedId;
    }


    /**
     * @param \Model\Region\Entity[] $nearestCity
     */
    public function setNearestCity($nearestCity) {
        $this->nearestCity = [];
        foreach ($nearestCity as $city) {
            $this->addNearestCity($city);
        }
    }

    /**
     * @return \Model\Region\Entity[]
     */
    public function getNearestCity() {
        return $this->nearestCity;
    }

    public function addNearestCity(\Model\Region\Entity $city) {
        $this->nearestCity[] = $city;
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
     * @param string $ui
     */
    public function setUi($ui) {
        $this->ui = (string)$ui;
    }

    /**
     * @return string
     */
    public function getUi() {
        return $this->ui;
    }

    /**
     * @param float $price
     */
    public function setPrice($price) {
        $this->price = (float)$price;
    }

    /**
     * @return float
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param int $statusId
     */
    public function setStatusId($statusId) {
        $this->statusId = $statusId ? (int)$statusId : null;
    }

    /**
     * @return int
     */
    public function getStatusId() {
        return $this->statusId;
    }

    /**
     * @return string
     */
    public function getNameWithCategory() {
        $name = $this->name;

        if ($this->rootCategory) {
            $name .= ' - ' . $this->rootCategory->getName();
        }

        return $name;
    }

    /**
     * @return \Model\Product\Category\Entity
     */
    public function getRootCategory() {
        return $this->rootCategory;
    }

    /**
     * @return bool
     */
    public function getIsBuyable() {
        return
            $this->getState() && $this->getState()->getIsBuyable()
            && (\App::config()->product['allowBuyOnlyInshop'] ? true : !$this->isInShopStockOnly())
            && $this->getPrice() !== null
            && $this->getStatusId() != 5
            ;
    }

    /**
     * @param Kit\Entity[] $kits
     */
    public function setKit(array $kits) {
        $this->kit = [];
        foreach ($kits as $kit) {
            $this->addKit($kit);
        }
    }

    /**
     * @param Kit\Entity $kit
     */
    public function addKit(Kit\Entity $kit) {
        $this->kit[] = $kit;
    }

    /**
     * @return Kit\Entity[]
     */
    public function getKit() {
        return $this->kit;
    }

    /**
     * @param boolean $isKitLocked
     */
    public function setIsKitLocked($isKitLocked) {
        $this->isKitLocked = (bool)$isKitLocked;
    }

    /**
     * @return boolean
     */
    public function getIsKitLocked() {
        return $this->isKitLocked;
    }

    /**
     * @return string
     */
    public function getPath() {
        return trim(preg_replace('/^\/product\//' , '', $this->link), '/');
    }

    /** Возвращает сток с максимальным количеством товара
     * @return Stock\Entity|null
     */
    public function getStockWithMaxQuantity(){

        if (empty($this->stock)) return null;

        $maxStock = $this->stock[0];
        foreach ($this->stock as $stock) {
            if ($stock->getQuantity() > $maxStock->getQuantity()) $maxStock = $stock;
        }

        return $maxStock;
    }

    /**
     * @return \Model\Product\Category\Entity
     */
    public function getParentCategory() {
        return $this->parentCategory;
    }

    public function setEan($ean) {
        $this->ean = $ean;
    }

    public function getEan() {
        return $this->ean;
    }

    public function setAvgScore($avgScore) {
        $this->avgScore = $avgScore;
    }

    public function getAvgScore() {
        return $this->avgScore;
    }

    public function setAvgStarScore($avgStarScore) {
        $this->avgStarScore = $avgStarScore;
    }

    public function getAvgStarScore() {
        return $this->avgStarScore;
    }

    public function setNumReviews($numReviews) {
        $this->numReviews = $numReviews;
    }

    public function getNumReviews() {
        return $this->numReviews;
    }


    /**
     * @param int|null $shopId
     * @return bool
     */
    public function isInShopShowroom($shopId = null) {
        foreach ($this->getStock() as $stock) {
            if ($shopId && ($stock->getShopId() != $shopId)) continue;

            if ($stock->getQuantityShowroom()) {
                return true;

                break;
            }
        }

        return false;
    }

    /**
     * @param int $shopId
     * @return bool
     */
    public function isInShop($shopId) {
        $shopId = (int)$shopId;
        if (!$shopId) return false;


        $return = false;
        foreach ($this->getStock() as $stock) {
            if (($stock->getShopId() == $shopId) && $stock->getQuantity()) {
                $return = true;
                break;
            }
        }

        return $return;
    }



    /**
     * @return bool
     */
    public function isInShopShowroomOnly() {
        return $this->isInShowroomsOnly;
    }


    /**
     * @return bool
     */
    public function isInShopStockOnly() {
        return $this->isInShopsOnly && !$this->hasPartnerStock;
    }

    /**
     * @return bool
     */
    public function isInShopOnly() {
        return $this->isInShopStockOnly() || $this->isInShopShowroomOnly();
    }

    public function calculateState($data = []) {

        //$inStore = false;
        $inStore = isset($data['state']['is_store']) ? (bool)$data['state']['is_store'] : null; // SITE-4659
        //$inShop = false;
        $inShop = isset($data['state']['is_shop']) ? (bool)$data['state']['is_shop'] : null; // SITE-4659
        $inShowroom = false;
        foreach ($this->getStock() as $stock) {
            /*
            if ($stock->getStoreId() && $stock->getQuantity()) {
                $inStore = true;
            }
            if ($stock->getShopId() && $stock->getQuantity()) { // есть на складе магазина
                $inShop = true;
            }
            */
            if ($stock->getShopId() && $stock->getQuantityShowroom()) { // есть на витрине магазина
                $inShowroom = true;
            }
        }

        $this->isOnlyFromPartner = false;
        foreach ($this->getPartnersOffer() as $partnerOffer) {
            if (!isset($partnerOffer['stock'][0])) continue;

            foreach ($partnerOffer['stock'] as $partnerStock) {
                if (!empty($partnerStock['quantity'])) {
                    $this->hasPartnerStock = true;
                    break;
                }
            }
        }
        $this->isOnlyFromPartner = !(bool)$this->getStock() && $this->hasPartnerStock;

        $this->isInShopsOnly = !$inStore && $inShop;
        $this->isInShowroomsOnly = !$inStore && !$inShop && $inShowroom;
    }

    /**
     * @param boolean $isUpsale
     */
    public function setIsUpsale($isUpsale) {
        $this->isUpsale = (bool)$isUpsale;
    }

    /**
     * @return boolean
     */
    public function getIsUpsale() {
        return $this->isUpsale;
    }

    /**
     * @param array $partnersOffer
     */
    public function setPartnersOffer($partnersOffer) {
        $this->partnersOffer = [];
        foreach ($partnersOffer as $offer) {
            $this->partnersOffer[] = $offer;
        }
    }

    /**
     * @return array
     */
    public function getPartnersOffer() {
        return $this->partnersOffer;
    }

    /** Возвращает наименование первого партнера
     * @return string|null
     */
    public function getPartnerName(){
        return !empty($this->partnersOffer) && isset($this->partnersOffer[0]['name']) ? $this->partnersOffer[0]['name'] : null;
    }

    /** Возвращает оферту первого партнера
     * @return string|null
     */
    public function getPartnerOfferLink() {
        return !empty($this->partnersOffer) && isset($this->partnersOffer[0]['offer']) ? $this->partnersOffer[0]['offer'] : null;
    }

    /**
     * @return boolean
     */
    public function isOnlyFromPartner() {
        return $this->isOnlyFromPartner;
    }

    /**
     * @return array|null
     */
    public function getSlotPartnerOffer() {
        foreach ($this->partnersOffer as $offer) {
            if (isset($offer['type']) && self::PARTNER_OFFER_TYPE_SLOT == $offer['type']) {
                return $offer + ['name' => null, 'offer' => null];
            }
        }

        return null;
    }

    public function setModel(Model\Entity $model = null) {
        $this->model = $model;
    }

    /**
     * @return Model\Entity|null
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * @param string $seoTitle
     */
    public function setSeoTitle($seoTitle) {
        $this->seoTitle = (string)$seoTitle;
    }

    /**
     * @return string
     */
    public function getSeoTitle() {
        return $this->seoTitle;
    }

    /**
     * @param string $seoKeywords
     */
    public function setSeoKeywords($seoKeywords) {
        $this->seoKeywords = (string)$seoKeywords;
    }

    /**
     * @return string
     */
    public function getSeoKeywords() {
        return $this->seoKeywords;
    }

    /**
     * @param string $seoDescription
     */
    public function setSeoDescription($seoDescription) {
        $this->seoDescription = (string)$seoDescription;
    }

    /**
     * @return string
     */
    public function getSeoDescription() {
        return $this->seoDescription;
    }

    /**
     * @return bool
     */
    public function isAvailable() {
        return ($this->getIsBuyable() || $this->isInShopOnly() || $this->isInShopStockOnly());
    }

    /**
     * @return bool
     */
    public function hasAvailableModels() {
        if ($this->getModel()) {
            foreach ($this->getModel()->getProperty() as $property) {
                foreach ($property->getOption() as $option) {
                    if ($option->getProduct()->isAvailable()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isSoldOut() {
        return
            $this->getRootCategory()
            && ('tchibo' === $this->getRootCategory()->getToken())
            && !$this->isAvailable()
            && !$this->hasAvailableModels()
            ;
    }

    /**
     * @param string|null $provider
     * @param string|null $tag
     * @return \Model\Media[]
     */
    public function getMedias($provider = null, $tag = null) {
        if ($provider === null && $tag === null) {
            return $this->medias;
        }

        $medias = [];
        foreach ($this->medias as $media) {
            if (($provider === null || $media->provider === $provider) && ($tag === null || in_array($tag, $media->tags, true))) {
                $medias[] = $media;
            }
        }

        return $medias;
    }

    public function getImageUrl(){
        return $this->getMainImageUrl('product_120');
    }

    public function getMainImageUrl($sourceType) {
        $images = $this->getMedias('image', 'main');
        if ($images) {
            $source = $images[0]->getSource($sourceType);
            if ($source) {
                return $source->url;
            }
        }

        return '';
    }

    public function getHoverImageUrl($sourceType) {
        $images = $this->getMedias('image', 'on_model');
        if ($images) {
            $source = $images[0]->getSource($sourceType);
            if ($source) {
                return $source->url;
            }
        }

        return '';
    }

    public function hasVideo() {
        foreach ($this->medias as $media) {
            if (in_array($media->provider, ['vimeo', 'youtube'], true)) {
                return true;
            }
        }

        return false;
    }

    public function has3d() {
        if ($this->json3d) {
            return true;
        }

        foreach ($this->medias as $media) {
            // Временно отключаем maybe3d html5 модели из-за проблем, описанных в SITE-3783
//            if (in_array($media->provider, ['megavisor', 'maybe3d', 'swf'], true)) {
            if (in_array($media->provider, ['megavisor', 'swf'], true) || ($media->provider === 'maybe3d' && $media->getSource('swf'))) {
                return true;
            }
        }

        return false;
    }

    /* Электронный сертификат от giftery.ru */
    public function isGifteryCertificate() {
        return $this->getUi() == $this::GIFTERY_UID;
    }

    /** Установка купонов для продукта
     * @param $coupons
     */
    public function setCoupons($coupons) {
        if (is_array($coupons)) {
            foreach ($coupons as $coupon) {
                if ($coupon instanceof Coupon) $this->coupons[] = $coupon;
            }
        }
    }

    /** Возращает купон с максимальным дискаунтом
     * @return Coupon|null
     */
    public function getBestCoupon() {
        $bestCoupon = null;
        $maxDiscount = 0;
        foreach ($this->coupons as $coupon) {
            if ($bestCoupon === null) $bestCoupon = $coupon;
            $currentDiscount = $coupon->getIsCurrency() ? $coupon->getPrice() : $coupon->getPrice() / 100 * $this->getPrice();
            if ($currentDiscount > $maxDiscount) {
                $maxDiscount = $currentDiscount;
                $bestCoupon = $coupon;
            }
        }

        return $bestCoupon;
    }

}