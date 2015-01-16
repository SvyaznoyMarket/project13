<?php

namespace Model\Product\Category;

use Model\Media;

class Entity extends BasicEntity {
    use \Model\MediaHostTrait;

    /** @var bool|null */
    protected $isFurniture;
    /** @var bool|null */
    protected $hasLine;
    /** @var string|null */
    protected $productView;
    /** @var string|null */
    protected $seoTitle;
    /** @var string|null */
    protected $seoKeywords;
    /** @var string|null */
    protected $seoDescription;
    /** @var string|null */
    protected $seoContent;
    /** @var \Model\Seo\Hotlink\Entity[] */
    protected $seoHotlinks = [];
    /** @var int|null */
    protected $productCount;
    /** @var int|null */
    protected $globalProductCount;
    /** @var bool|null */
    protected $hasChild;
    /** @var float|null */
    protected $priceChangePercentTrigger;
    /** @var bool|null */
    protected $priceChangeTriggerEnabled;
    /** @var Media[] */
    public $medias = [];
    /** @var array */
    public $catalogJson = [];
    /** @var \Model\GridCell\Entity[] */
    public $grid = [];
    /** @var Entity|null */
    protected $parent;
    /** @var Entity|null */
    protected $root;
    /** @var Entity[] */
    protected $ancestor = [];
    /** @var Entity[] */
    protected $child = [];

    public function __construct($data = []) {
        $data['price_change_trigger_enabled'] = true;
        $data['price_change_percent_trigger'] = 90;

        if (isset($data['id'])) $this->setId($data['id']);
        if (isset($data['ui'])) $this->setUi($data['ui']); // Берётся из http://api.enter.ru/v2/product/get (из элемента "category")
        if (isset($data['uid'])) $this->setUi($data['uid']); // Берётся из https://scms.enter.ru/category/get/v1, https://scms.enter.ru/category/gets и http://search.enter.ru/category/tree

        if (isset($data['parent_id'])) $this->setParentId($data['parent_id']); // Берётся из http://search.enter.ru/category/tree (из элемента "children") и http://api.enter.ru/v2/product/get (из элемента "category")
        if (isset($data['parent']['id'])) $this->setParentId($data['parent']['id']); // Берётся из https://scms.enter.ru/category/get/v1, https://scms.enter.ru/category/gets

        if (isset($data['is_furniture'])) $this->setIsFurniture($data['is_furniture']);
        if (isset($data['name'])) $this->setName($data['name']);

        if (isset($data['link'])) $this->setLink($data['link']); // Берётся из http://search.enter.ru/category/tree (из элемента "children") и http://api.enter.ru/v2/product/get (из элемента "category")
        if (isset($data['url'])) $this->setLink($data['url']); // Берётся из https://scms.enter.ru/category/get/v1, https://scms.enter.ru/category/gets

        if (isset($data['token'])) $this->setToken($data['token']); // Берётся из http://search.enter.ru/category/tree (из элемента "children") и http://api.enter.ru/v2/product/get (из элемента "category")
        if (isset($data['slug'])) $this->setToken($data['slug']); // Берётся из https://scms.enter.ru/category/get/v1, https://scms.enter.ru/category/gets

        if (isset($data['media_image'])) $this->setImage($data['media_image']);
        if (isset($data['media_image_480x480'])) $this->image480x480 = $data['media_image_480x480'];

        // Берётся из https://scms.enter.ru/category/get/v1, https://scms.enter.ru/category/gets
        if (isset($data['medias']) && is_array($data['medias'])) {
            foreach ($data['medias'] as $media) {
                $this->medias[] = new Media($media);
            }
        }

        if (isset($data['has_line'])) $this->setHasLine($data['has_line']);
        if (isset($data['product_view_id'])) $this->setProductView($data['product_view_id']);
        if (isset($data['level'])) $this->setLevel($data['level']);

        if (isset($data['title'])) $this->setSeoTitle($data['title']);
        if (isset($data['meta_keywords'])) $this->setSeoKeywords($data['meta_keywords']);
        if (isset($data['meta_description'])) $this->setSeoDescription($data['meta_description']);
        if (isset($data['content'])) $this->setSeoContent($data['content']);

        if (isset($data['property']['seo']['hotlinks'])) {
            $this->setSeoHotlinks($data['property']['seo']['hotlinks']);
        }

        if (isset($data['product_count'])) $this->setProductCount($data['product_count']);
        if (isset($data['product_count_global'])) $this->setGlobalProductCount($data['product_count_global']);
        if (isset($data['has_children'])) $this->setHasChild($data['has_children']);
        if (isset($data['price_change_percent_trigger'])) $this->setPriceChangePercentTrigger($data['price_change_percent_trigger'] / 100);
        if (isset($data['price_change_trigger_enabled'])) $this->setPriceChangeTriggerEnabled($data['price_change_trigger_enabled']);

        $this->catalogJson = $this->convertCatalogJsonToOldFormat($data);

        if (isset($data['grid']['items']) && is_array($data['grid']['items'])) {
            foreach ($data['grid']['items'] as $item) {
                if (is_array($item)) {
                    $this->grid[] = new \Model\GridCell\Entity($item);
                }
            }
        }
    }

    /**
     * Является ли категория корневым узлом дерева (root node)
     *
     * @return bool
     */
    public function isRoot() {
        return 1 == $this->level;
    }

    /**
     * Является ли категория узлом дерева с дочерними элементами (inner node)
     *
     * @return bool
     */
    public function isBranch() {
        return $this->hasChild;
    }

    /**
     * Является ли категория узлом дерева без дочерних элементов (outer node)
     *
     * @return bool
     */
    public function isLeaf() {
        return !$this->hasChild;
    }

    /**
     * @param boolean $hasLine
     */
    public function setHasLine($hasLine) {
        $this->hasLine = (bool)$hasLine;
    }

    /**
     * @return boolean
     */
    public function getHasLine() {
        return $this->hasLine;
    }

    /**
     * @param boolean $isFurniture
     */
    public function setIsFurniture($isFurniture) {
        $this->isFurniture = (bool)$isFurniture;
    }

    /**
     * @return boolean
     */
    public function getIsFurniture() {
        return $this->isFurniture;
    }

    /**
     * @param int $productCount
     */
    public function setProductCount($productCount)
    {
        $this->productCount = (int)$productCount;
    }

    /**
     * @return int
     */
    public function getProductCount()
    {
        return $this->productCount;
    }

    /**
     * @param int $globalProductCount
     */
    public function setGlobalProductCount($globalProductCount) {
        $this->globalProductCount = (int)$globalProductCount;
    }

    /**
     * @return int
     */
    public function getGlobalProductCount() {
        return $this->globalProductCount;
    }

    /**
     * @param string $productView
     */
    public function setProductView($productView) {
        if ((int)$productView > 0) {
            $idToNameMap = [
                1 => self::PRODUCT_VIEW_COMPACT,
                2 => self::PRODUCT_VIEW_EXPANDED,
                3 => self::PRODUCT_VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION,
                4 => self::PRODUCT_VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION,
                5 => self::PRODUCT_VIEW_LIGHT_WITHOUT_DESCRIPTION,
            ];

            if (array_key_exists($productView, $idToNameMap)) {
                $this->productView = $idToNameMap[$productView];
            }
        } else {
            $this->productView = (string)$productView;
        }
    }

    /**
     * @return string
     */
    public function getProductView() {
        return $this->productView;
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
     * @param string $seoText
     */
    public function setSeoContent($seoText) {
        $this->seoContent = (string)$seoText;
    }

    /**
     * @return string
     */
    public function getSeoContent() {
        return $this->seoContent;
    }

    public function setSeoHotlinks(array $hotlinks) {
        $this->seoHotlinks = [];
        foreach ($hotlinks as $hotlink) {
            $this->seoHotlinks[] = new \Model\Seo\Hotlink\Entity($hotlink);
        }
    }

    /**
     * @return \Model\Seo\Hotlink\Entity[]
     */
    public function getSeoHotlinks() {
        return $this->seoHotlinks;
    }

    /**
     * @param bool $hasChild
     */
    public function setHasChild($hasChild) {
        $this->hasChild = (bool)$hasChild;
    }

    /**
     * @return bool
     */
    public function getHasChild() {
        return $this->hasChild;
    }

    /**
     * @param float $priceChangePercentTrigger
     */
    public function setPriceChangePercentTrigger($priceChangePercentTrigger) {
        $this->priceChangePercentTrigger = (float)$priceChangePercentTrigger;
    }

    /**
     * @return float
     */
    public function getPriceChangePercentTrigger() {
        return $this->priceChangePercentTrigger;
    }

    /**
     * @param bool $priceChangeTriggerEnabled
     */
    public function setPriceChangeTriggerEnabled($priceChangeTriggerEnabled) {
        $this->priceChangeTriggerEnabled = (bool)$priceChangeTriggerEnabled;
    }

    /**
     * @return bool
     */
    public function getPriceChangeTriggerEnabled() {
        return $this->priceChangeTriggerEnabled;
    }

    public function getImageUrl($size = 0) {
        if ($this->image) {
            if (preg_match('/^(https?|ftp)\:\/\//i', $this->image)) {
                if (0 == $size) {
                    return $this->image;
                } else if (3 == $size) {
                    return $this->image480x480;
                }
            } else {
                $urls = \App::config()->productCategory['url'];
                return $this->getHost() . $urls[$size] . $this->image;
            }
        } else if ($this->medias) {
            if (0 == $size) {
                $source = $this->getMediaSource('category_163x163');
            } else if (3 == $size) {
                $source = $this->getMediaSource('category_480x480');
            } else {
                $source = null;
            }

            if ($source) {
                return $source->url;
            }

        }
    }

    /**
     * @param string $sourceType
     * @param string $mediaProvider
     * @param string $mediaTag
     * @return Media\Source|null
     */
    private function getMediaSource($sourceType, $mediaTag = 'main', $mediaProvider = 'image') {
        foreach ($this->medias as $media) {
            if ($media->provider === $mediaProvider && in_array($mediaTag, $media->tags, true)) {
                foreach ($media->sources as $source) {
                    if ($source->type === $sourceType) {
                        return $source;
                    }
                }
            }
        }

        return null;
    }

    public function setParent(Entity $parent = null) {
        $this->parent = $parent;
    }

    /**
     * @return Entity|null
     */
    public function getParent() {
        return $this->parent ?: end($this->ancestor);
    }

    /**
     * @return Entity
     */
    public function getRoot() {
        return $this->root ?: reset($this->ancestor);
    }

    public function addAncestor(Entity $ancestor) {
        $this->ancestor[] = $ancestor;
    }

    /**
     * @return Entity[]
     */
    public function getAncestor() {
        return $this->ancestor;
    }

    public function addChild(Entity $child) {
        $this->child[] = $child;
    }

    /**
     * @return Entity[]
     */
    public function getChild() {
        return $this->child;
    }

    public function isV2Root() {
        return ($this->isNewMainPage() && '616e6afd-fd4d-4ff4-9fe1-8f78236d9be6' === $this->getUi()); // Корневая бытовой техники
    }

    public function isV2() {
        $root = $this->getRoot();
        if (!$root) {
            $root = $this;
        }

        if ($this->isNewMainPage()) {
            // Бытовая техника
            if ($root->getUi() === '616e6afd-fd4d-4ff4-9fe1-8f78236d9be6') {
                return true;
            }

            // Мебель
            if ($root->getUi() === 'f7a2f781-c776-4342-81e8-ab2ebe24c51a') {
                return true;
            }
        }

        return false;
    }

    public function isV2Furniture() {
        $root = $this->getRoot();
        if (!$root) {
            $root = $this;
        }

        if ($this->isNewMainPage()) {
            // Мебель
            if ($root->getUi() === 'f7a2f781-c776-4342-81e8-ab2ebe24c51a') {
                return true;
            }
        }

        return false;
    }

    private function convertCatalogJsonToOldFormat($data) {
        $result = [];

        if (is_array($data)) {
            if (isset($data['uid'])) {
                $result['ui'] = $data['uid'];
            }

            if (isset($data['property']['bannerPlaceholder'])) {
                $result['bannerPlaceholder'] = $data['property']['bannerPlaceholder'];
            }

            if (isset($data['property']['smartchoice']['enabled'])) {
                $result['smartchoice'] = $data['property']['smartchoice']['enabled'];
            }

            if (isset($data['property']['appearance']['category_class'])) {
                $result['category_class'] = $data['property']['appearance']['category_class'];
            }

            if (isset($data['property']['appearance']['promo_token'])) {
                $result['promo_token'] = $data['property']['appearance']['promo_token'];
            }

            if (isset($data['property']['appearance']['use_logo'])) {
                $result['use_logo'] = $data['property']['appearance']['use_logo'];
            }

            if (isset($data['property']['appearance']['logo_path'])) {
                $result['logo_path'] = $data['property']['appearance']['logo_path'];
            }

            if (isset($data['property']['appearance']['use_lens'])) {
                $result['use_lens'] = $data['property']['appearance']['use_lens'];
            }

            if (isset($data['property']['appearance']['is_new'])) {
                $result['is_new'] = (bool)$data['property']['appearance']['is_new'];
            }

            if (isset($data['property']['appearance']['default']['listing_style'])) {
                $result['listing_style'] = $data['property']['appearance']['default']['listing_style'];
            }

            if (isset($data['property']['appearance']['default']['promo_style'])) {
                $result['promo_style'] = $data['property']['appearance']['default']['promo_style'];
            }

            if (isset($data['property']['appearance']['pandora']['sub_category_filters_exclude']) && is_array($data['property']['appearance']['pandora']['sub_category_filters_exclude'])) {
                $result['sub_category_filters_exclude'] = [];
                foreach ($data['property']['appearance']['pandora']['sub_category_filters_exclude'] as $item) {
                    if (isset($item['filter_token'])) {
                        $result['sub_category_filters_exclude'][] = $item['filter_token'];
                    }
                }
            }

            if (isset($data['property']['appearance']['pandora']['sub_category_filter_menu'])) {
                $result['sub_category_filter_menu'] = $data['property']['appearance']['pandora']['sub_category_filter_menu'];
            }

            if (isset($data['property']['appearance']['tchibo']['root_id'])) {
                $result['root_category_menu']['root_id'] = $data['property']['appearance']['tchibo']['root_id'];
            }

            if (isset($data['property']['appearance']['tchibo']['image'])) {
                $result['root_category_menu']['image'] = $data['property']['appearance']['tchibo']['image'];
            }

            if (isset($data['property']['appearance']['tchibo']['red_category_id'])) {
                $result['tchibo_menu']['style']['name'] = [$data['property']['appearance']['tchibo']['red_category_id'] => 'color:red;'];
            }

            if (isset($data['property']['appearance']['show_branch_menu'])) {
                $result['show_branch_menu'] = $data['property']['appearance']['show_branch_menu'];
            }

            if (isset($data['property']['appearance']['show_side_panels'])) {
                $result['show_side_panels'] = $data['property']['appearance']['show_side_panels'];
            }

            if (isset($data['property']['sort']['json'])) {
                $result['sort'] = $data['property']['sort']['json'];
            }

            if (isset($data['property']['related_categories']['related_categories'])) {
                $result['related_categories'] = $data['property']['related_categories']['related_categories'];
            }

            if (isset($data['property']['search_hints']['search_hints']) && is_array($data['property']['search_hints']['search_hints'])) {
                $result['search_hints'] = [];
                foreach ($data['property']['search_hints']['search_hints'] as $val) {
                    if (isset($val['search_string'])) {
                        $result['search_hints'][] = $val['search_string'];
                    }
                }
            }

            if (isset($data['property']['promo_slider'])) {
                $result['promo_slider'] = $data['property']['promo_slider'];
            }

            if (isset($data['property']['products']['accessory_category_token'])) {
                $result['accessory_category_token'] = $data['property']['products']['accessory_category_token'];
            }
        }

        return $result;
    }
}