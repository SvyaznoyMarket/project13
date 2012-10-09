<?php

namespace Model\Product;

class Entity extends BasicEntity {
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
    protected $article;
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
    protected $category = array();
    /** @var int */
    protected $connectedViewId;
    /** @var Property\Group\Entity[] */
    protected $propertyGroup = array();
    /** @var Property\Entity[] */
    protected $property = array();
    /** @var \Model\Tag\Entity[] */
    protected $tag = array();
    /** @var Media\Entity[] */
    protected $media = array();
    /** @var \Model\Brand\Entity|null */
    protected $brand;
    /** @var Label\Entity|null */
    protected $label;
    /** @var Type\Entity|null */
    protected $type;
    /** @var int */
    protected $commentCount;
    /** @var int */
    protected $priceAverage;
    /** @var int */
    protected $priceOld;
    /** @var Stock\Entity[] */
    protected $stock = array();
    /** @var Service\Entity[] */
    protected $service = array();
    /** @var Line\Entity */
    protected $line;
    /** @var Kit\Entity[] */
    protected $kit = array();
    /** @var Model\Entity */
    protected $model;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('view_id', $data)) $this->setViewId($data['view_id']);
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('set_id', $data)) $this->setSetId($data['set_id']);
        if (array_key_exists('label_id', $data)) $this->setLabelId($data['label_id']);
        if (array_key_exists('is_model', $data)) $this->setIsModel($data['is_model']);
        if (array_key_exists('is_primary_line', $data)) $this->setIsPrimaryLine($data['is_primary_line']);
        if (array_key_exists('model_id', $data)) $this->setModelId($data['model_id']);
        if (array_key_exists('score', $data)) $this->setScore($data['score']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name_web', $data)) $this->setWebName($data['name_web']);
        if (array_key_exists('prefix', $data)) $this->setPrefix($data['prefix']);
        if (array_key_exists('article', $data)) $this->setArticle($data['article']);
        if (array_key_exists('bar_code', $data)) $this->setBarcode($data['bar_code']);
        if (array_key_exists('tagline', $data)) $this->setTagline($data['tagline']);
        if (array_key_exists('announce', $data)) $this->setAnnounce($data['announce']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('rating', $data)) $this->setRating($data['rating']);
        if (array_key_exists('rating_count', $data)) $this->setRatingCount($data['rating_count']);
        if (array_key_exists('category', $data) && is_array($data['category'])) {
            $categoryData = reset($data['category']);
            if ((bool)$categoryData) $this->setMainCategory(new Category\Entity($categoryData));

            foreach ($data['category'] as $categoryData) {
                $this->addCategory(new Category\Entity($categoryData));
            }
        }
        if (array_key_exists('connected_products_view_mode', $data)) $this->setConnectedViewId($data['connected_products_view_mode']);
        if (array_key_exists('property_group', $data) && is_array($data['property_group'])) $this->setPropertyGroup(array_map(function($data) {
            return new Property\Group\Entity($data);
        }, $data['property_group']));
        if (array_key_exists('property', $data) && is_array($data['property'])) $this->setProperty(array_map(function($data) {
            return new Property\Entity($data);
        }, $data['property']));
        if (array_key_exists('tag', $data) && is_array($data['tag'])) $this->setTag(array_map(function($data) {
            return new \Model\Tag\Entity($data);
        }, $data['tag']));
        if (array_key_exists('media', $data) && is_array($data['media'])) $this->setMedia(array_map(function($data) {
            return new Media\Entity($data);
        }, $data['media']));
        if (array_key_exists('brand', $data) && (bool)$data['brand']) $this->setBrand(new \Model\Brand\Entity($data['brand']));
        if (array_key_exists('label', $data) && (bool)$data['label']) $this->setLabel(new Label\Entity($data['label']));
        if (array_key_exists('type', $data) && (bool)$data['type']) $this->setType(new Type\Entity($data['type']));
        if (array_key_exists('comment_count', $data)) $this->setCommentCount($data['comment_count']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('price_average', $data)) $this->setPriceAverage($data['price_average']);
        if (array_key_exists('price_old', $data)) $this->setPriceOld($data['price_old']);
        if (array_key_exists('state', $data) && (bool)$data['state']) $this->setState(new State\Entity($data['state']));
        if (array_key_exists('stock', $data) && is_array($data['stock'])) $this->setStock(array_map(function($data) {
            return new Stock\Entity($data);
        }, $data['stock']));
        if (array_key_exists('service', $data) && is_array($data['service'])) $this->setService(array_map(function($data) {
            return new Service\Entity($data);
        }, $data['service']));
        if (array_key_exists('line', $data) && (bool)$data['line']) $this->setLine(new Line\Entity($data['line']));
        if (array_key_exists('kit', $data) && is_array($data['kit'])) $this->setKit(array_map(function($data) {
            return new Kit\Entity($data);
        }, $data['kit']));
        // TODO: related, accessories, model
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
     * @param Category\Entity $categories
     */
    public function setCategory(array $categories) {
        $this->category = array();
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

    /**
     * @return string
     */
    public function getAnnounce() {
        return $this->announce;
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
     * @param int|null $connectedViewId
     */
    public function setConnectedViewId($connectedViewId = null) {
        $this->connectedViewId = $connectedViewId ? (int)$connectedViewId : null;
    }

    /**
     * @return int
     */
    public function getConnectedViewId() {
        return $this->connectedViewId;
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

    public function setMedia(array $mediaList) {
        $this->media = array();
        foreach ($mediaList as $media) {
            $this->addMedia($media);
        }
    }

    public function addMedia(Media\Entity $media) {
        $this->media[] = $media;
    }

    public function getMedia() {
        return $this->media;
    }

    /**
     * @param int $modelId
     */
    public function setModelId($modelId = null) {
        $this->modelId = $modelId ? (int)$modelId : null;
    }

    /**
     * @return int
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
        $this->property = array();
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

    public function setPropertyGroup(array $propertyGroups) {
        $this->propertyGroup = array();
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

    public function setStock(array $stocks) {
        $this->stock = array();
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

    public function setTag(array $tags) {
        $this->tag = array();
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
        $this->service = array();
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
     * @param \Model\Product\Line\Entity $line
     */
    public function setLine(Line\Entity $line = null) {
        $this->line = $line;
    }

    /**
     * @return \Model\Product\Line\Entity
     */
    public function getLine() {
        return $this->line;
    }

    public function setKit(array $kits) {
        $this->kit = array();
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
}