<?php

namespace Model\Product;

use Model\Media;
use Model\Product\Delivery\ProductDelivery;
use Model\EnterprizeCoupon\Entity as Coupon;

class Entity {
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
    /** @var \Model\Product\Model|null */
    public $model;
    /** @var string|null */
    protected $seoTitle;
    /** @var string|null */
    protected $seoKeywords;
    /** @var string|null */
    protected $seoDescription;
    /** @var string|null */
    public $seoText;
    /** @var string */
    protected $webName;
    /** @var string */
    protected $prefix;
    /** @var string */
    protected $tagline;
    /** @var string */
    protected $description;
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
    protected $priceOld;
    /** @var [] */
    protected $groupedProperties = [];
    /** @var array */
    protected $accessoryId = [];
    /** @var array */
    protected $relatedId = [];
    /** @var ProductDelivery|null */
    public $delivery;
    /** @var \Model\Media[] */
    public $medias = [];
    /** @var array */
    public $json3d = [];
    /** @var Coupon[] */
    public $coupons = [];
    private $isImportedFromCore = false;
    private $isImportedFromScms = false;
    /** @var bool */
    public $needPrepayment = false;
    /**
     * @var \Model\Product\Trustfactor[]
     */
    public $trustfactors = [];

    public function __construct($data = []) {
        $this->importFromCore($data);
    }

    public function importFromCore($data = []) {
        if (isset($data['id'])) $this->setId($data['id']);
        else if (isset($data['core_id'])) $this->setId($data['core_id']);
        if (isset($data['ui'])) $this->setUi($data['ui']);
        if (isset($data['uid'])) $this->setUi($data['uid']); // для scms
        if (isset($data['status_id'])) $this->setStatusId($data['status_id']);
        if (isset($data['article'])) $this->setArticle($data['article']);
        if (isset($data['bar_code'])) $this->setBarcode($data['bar_code']);
        if (isset($data['price'])) $this->setPrice($data['price']);
        if (isset($data['state']) && (bool)$data['state']) $this->setState(new State\Entity($data['state']));
        if (isset($data['stock']) && is_array($data['stock'])) $this->setStock(array_map(function($data) {
            return new Stock\Entity($data);
        }, $data['stock']));
        if (isset($data['partners_offer'])) $this->setPartnersOffer(array_map(function($v) { return $v; }, $data['partners_offer']));
        if (isset($data['ean'])) $this->setEan($data['ean']);

        if (array_key_exists('kit', $data) && is_array($data['kit'])) $this->setKit(array_map(function($data) {
            return new Kit\Entity($data);
        }, $data['kit']));
        if (array_key_exists('is_kit_locked', $data)) $this->setIsKitLocked($data['is_kit_locked']);

        $this->calculateState($data);

        if (array_key_exists('type', $data) && (bool)$data['type']) $this->setType(new Type\Entity($data['type']));
        if (array_key_exists('price_old', $data)) $this->setPriceOld($data['price_old']);
        if (array_key_exists('related', $data)) $this->setRelatedId($data['related']);
        if (array_key_exists('accessories', $data)) $this->setAccessoryId($data['accessories']);

        $this->isImportedFromCore = true;
        $this->fixOldPrice();
    }

    public function importFromScms($data = []) {
        $templateHelper = new \Helper\TemplateHelper();

        if (isset($data['slug'])) $this->token = (string)$data['slug'];
        if (isset($data['url'])) $this->link = (string)$data['url'];
        if (isset($data['name'])) $this->setName($templateHelper->unescape($data['name'])); // Редакция в 1С не использует HTML сущности и теги в данном поле
        if (isset($data['name_web'])) $this->setWebName($templateHelper->unescape($data['name_web'])); // Редакция в 1С не использует HTML сущности и теги в данном поле
        if (isset($data['name_prefix'])) $this->setPrefix($templateHelper->unescape($data['name_prefix'])); // Редакция в 1С не использует HTML сущности и теги в данном поле
        if (isset($data['tagline'])) $this->tagline = (string)$data['tagline'];
        if (isset($data['description'])) $this->description = (string)$data['description'];

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

        if (!empty($data['brand'])) {
            $this->setBrand(new \Model\Brand\Entity([
                'ui'        => @$data['brand']['uid'],
                'id'        => @$data['brand']['core_id'],
                'token'     => @$data['brand']['slug'],
                'name'      => @$data['brand']['name'],
                'medias'    => @$data['brand']['medias']
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

        if (isset($data['seo_text']) && is_string($data['seo_text'])) {
            $this->seoText = $data['seo_text'];
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
        if (((!$this->getTagline() && !$this->model && !$this->getDescription() && $propertiesCount < 16) || $propertiesCount < 8) && !$this->hasLongProperties()) {
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

        if (isset($data['trustfactors']) && is_array($data['trustfactors'])) {
            foreach ($data['trustfactors'] as $trustfactor) {
                if (is_array($trustfactor)) {
                    $this->trustfactors[] = new Trustfactor($trustfactor);
                }
            }
        }

        // Трастфакторы "Спасибо от Сбербанка" и Много.ру не должны отображаться на партнерских товарах
        if ($this->getPartnersOffer() && !$this->hasSordexPartner()) {
            foreach ($this->trustfactors as $key => $trustfactor) {
                if ('right' === $trustfactor->type && in_array($trustfactor->uid, [Trustfactor::UID_MNOGO_RU, Trustfactor::UID_SBERBANK_SPASIBO])) {
                    unset($this->trustfactors[$key]);
                }
            }

            $this->trustfactors = array_values($this->trustfactors);
        }

        $this->isImportedFromScms = true;
        $this->fixOldPrice();
    }

    public function importModelFromScms($data) {
        if (!empty($data['model']['property']) && !empty($data['model']['items'])) {
            $this->model = new \Model\Product\Model($data['model']);
        }
    }

    /**
     * Т.к. из метода api.enter.ru/v2/product/get-v3 была убрана связь между выводом старой цены и наличием шильдика,
     * реализуем эту связь пока здесь (подробности в CORE-2936).
     *
     * Поскольку порядок вызова методов importFromCore и importFromScms может меняться, данный метод должен вызываться
     * в каждом из них.
     */
    private function fixOldPrice() {
        if ($this->isImportedFromCore && $this->isImportedFromScms && (!$this->label || !$this->label->affectPrice)) {
            $this->priceOld = null;
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
     * @param array|mixed $queryParams
     * @return string
     */
    public function getLink($queryParams = []) {
        if ($queryParams) {
            return $this->link . (strpos($this->link, '?') === false ? '?' : '&') . http_build_query($queryParams);
        } else {
            return $this->link;
        }
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
        if (is_array($relatedId)) {
            $this->relatedId = $relatedId;
        }
    }

    /**
     * @return array
     */
    public function getRelatedId() {
        return $this->relatedId;
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
            ($this->getState() && $this->getState()->getIsBuyable()
            && (\App::config()->product['allowBuyOnlyInshop'] ? true : !$this->isInShopStockOnly())
            && $this->getPrice() !== null
            && $this->getStatusId() != 5)
            || $this->isGifteryCertificate()
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

    /**
     * @return bool
     */
    public function hasSordexPartner() {
        $return = false;
        foreach ($this->partnersOffer as $offer) {
            if ('22cda64d-352a-11e5-93fc-288023e9c8ac' === $offer['id']) {
                $return = true;
                break;
            }
        }

        return $return;
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
     * У товаров, полученных методом http://api.enter.ru/v2/product/from-model, проверять наличие моделей не требуется,
     * т.к. данный метод всегда возвращает модель товара, которая в наличии (см. комментарии на
     * https://wiki.enter.ru/pages/viewpage.action?pageId=21569552)
     * @return bool
     */
    public function hasAvailableModels() {
        // https://scms.enter.ru/api/product/get-models всегда возвращает лишь товары в наличии
        return ($this->model && $this->model->property && $this->model->property->option);
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

    /** JSON для Google Ecommerce Analytic
     * @return string
     */
    public function ecommerceData(){

        $category = '';

        if ($cat = $this->parentCategory) {
            while ($cat) {
                $category = $cat->getName() . ($cat == $this->parentCategory ? '' : ' / ') . $category;
                $cat = $cat->getParent();
            }
        }

        return json_encode([
            'id'        => $this->getBarcode(),
            'name'      => $this->getName(),
            'price'     => $this->getPrice(),
            'brand'     => $this->getBrand() ? $this->getBrand()->getName() : '',
            'category'  => $category,
            'coupon'    => $this->label ? $this->label->name : ''
        ], JSON_UNESCAPED_UNICODE|JSON_HEX_APOS);
    }

    /**
     * @return bool
     */
    public function isOneClickAvailable() {
        return
            !\Session\AbTest\ABHelperTrait::isOneClickOnly()
            && !$this->needPrepayment
        ;
    }
}