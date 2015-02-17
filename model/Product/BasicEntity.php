<?php

namespace Model\Product;

class BasicEntity {
    use \Model\MediaHostTrait;

    const LABEL_ID_PODARI_ZHIZN = 17;

    /** @var string|null */
    protected $ui;
    /** @var int|null */
    protected $id;
    /** @var string|null */
    protected $barcode;
    /** @var string|null */
    protected $article;
    /** @var string|null */
    protected $name;
    /** @var string|null */
    protected $link;
    /** @var string|null */
    protected $token;
    /** @var string|null */
    protected $image;
    /** @var int|null */
    protected $price;
    /** @var State\Entity|null */
    protected $state;
    /** @var int|null */
    protected $statusId;
    /** @var Line\Entity */
    protected $line;
    /** @var Category\Entity */
    protected $mainCategory;
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
    /** @var \Model\Media[] */
    public $medias = [];
    /** @var array */
    public $json3d = [];

    public function __construct(array $data = []) {
        if (isset($data['id'])) $this->setId($data['id']);
        if (isset($data['ui'])) $this->setUi($data['ui']);
        if (isset($data['status_id'])) $this->setStatusId($data['status_id']);
        if (isset($data['name'])) $this->setName($data['name']);
        if (isset($data['link'])) $this->setLink($data['link']);
        if (isset($data['token'])) $this->setToken($data['token']);
        if (isset($data['article'])) $this->setArticle($data['article']);
        if (isset($data['bar_code'])) $this->setBarcode($data['bar_code']);
        if (isset($data['media_image'])) $this->setImage($data['media_image']);
        if (isset($data['category']) && (bool)$data['category']) {
            $categoryData = reset($data['category']);
            if ((bool)$categoryData) $this->setMainCategory(new Category\Entity($categoryData));

            $categoryData = end($data['category']);
            if ((bool)$categoryData) $this->setParentCategory(new Category\Entity($categoryData));
        };
        if (isset($data['price'])) $this->setPrice($data['price']);
        if (isset($data['state']) && (bool)$data['state']) $this->setState(new State\Entity($data['state']));
        if (isset($data['line']) && (bool)$data['line']) $this->setLine(new Line\Entity($data['line']));
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
        if (isset($data['title'])) $this->setSeoTitle($data['title']);
        if (isset($data['meta_keywords'])) $this->setSeoKeywords($data['meta_keywords']);
        if (isset($data['meta_description'])) $this->setSeoDescription($data['meta_description']);

        $this->calculateState($data);
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
        return  $this->link;
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
        //return true;
        return
            $this->getState() && $this->getState()->getIsBuyable()
            && (\App::config()->product['allowBuyOnlyInshop'] ? true : !$this->isInShopStockOnly())
        ;
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

    /**
     * @return Stock\Entity[]
     */
    public function getStock() {
        return $this->stock;
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

    public function setAvgScore($avgScore)
    {
        $this->avgScore = $avgScore;
    }

    public function getAvgScore()
    {
        return $this->avgScore;
    }

    public function setAvgStarScore($avgStarScore)
    {
        $this->avgStarScore = $avgStarScore;
    }

    public function getAvgStarScore()
    {
        return $this->avgStarScore;
    }

    public function setNumReviews($numReviews)
    {
        $this->numReviews = $numReviews;
    }

    public function getNumReviews()
    {
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
    public function setPartnersOffer($partnersOffer)
    {
        $this->partnersOffer = [];
        foreach ($partnersOffer as $offer) {
            $this->partnersOffer[] = $offer;
        }
    }

    /**
     * @return array
     */
    public function getPartnersOffer()
    {
        return $this->partnersOffer;
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
    public function getSlotPartnerOffer()
    {
        foreach ($this->partnersOffer as $offer) {
            if (isset($offer['type']) && 2 == $offer['type']) {
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
            $this->getMainCategory()
            && ('tchibo' === $this->getMainCategory()->getToken())
            && !$this->isAvailable()
            && !$this->hasAvailableModels()
        ;
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
            if (in_array($media->provider, ['megavisor', 'swf'], true) || ($media->provider === 'maybe3d' && $media->getSourceByType('swf'))) {
                return true;
            }
        }

        return false;
    }
}