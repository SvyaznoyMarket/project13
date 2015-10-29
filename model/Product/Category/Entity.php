<?php

namespace Model\Product\Category;

use Model\Media;

class Entity extends BasicEntity {
    const FAKE_SHOP_TOKEN = 'shop';

    /** @var bool Является ли категория главной для товара */
    public $isMain = false;
    /** @var bool|null */
    protected $isFurniture;
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
    /** @var bool|null */
    protected $hasChild;
    /** @var float|null */
    protected $priceChangePercentTrigger;
    /** @var bool|null */
    protected $priceChangeTriggerEnabled;
    /** @var string|null */
    protected $image;
    /** @var string|null */
    protected $image480x480;
    /** @var Media[] */
    public $medias = [];
    /** @var array */
    public $catalogJson = [];
    /** @var \Model\GridCell\Entity[] */
    public $grid = [];
    /** @var Entity|null */
    protected $parent;
    /** @var Entity[] */
    protected $ancestor = [];
    /** @var Entity[] */
    protected $child = [];
    /** @var Config */
    public $config;
    /**
     * Вид листинга (с учётом пользовательского выбора)
     * @var ListingView
     */
    public $listingView;

    public function __construct($data = []) {
        $templateHelper = new \Helper\TemplateHelper();
        
        $data['price_change_trigger_enabled'] = true;
        $data['price_change_percent_trigger'] = 90;

        if (isset($data['id'])) $this->setId($data['id']);
        if (isset($data['core_id'])) $this->setId($data['core_id']); // Берётся из методов scms
        if (isset($data['ui'])) $this->setUi($data['ui']); // Берётся из http://api.enter.ru/v2/product/get (из элемента "category")
        if (isset($data['uid'])) $this->setUi($data['uid']); // Берётся из https://scms.enter.ru/category/get/v1, https://scms.enter.ru/category/gets и http://search.enter.ru/category/tree

        if (isset($data['parent_id'])) $this->setParentId($data['parent_id']); // Берётся из http://search.enter.ru/category/tree (из элемента "children") и http://api.enter.ru/v2/product/get (из элемента "category")
        if (isset($data['parent']['id'])) $this->setParentId($data['parent']['id']); // Берётся из https://scms.enter.ru/category/get/v1, https://scms.enter.ru/category/gets
        if (isset($data['parent']['core_id'])) $this->setParentId($data['parent']['core_id']); // Берётся из https://scms.enter.ru/product/get-description/v1, https://scms.enter.ru/category/gets

        if (isset($data['main'])) $this->isMain = (bool)$data['main'];
        if (isset($data['is_furniture'])) $this->setIsFurniture($data['is_furniture']);
        if (isset($data['name'])) $this->setName($data['name']);

        if (isset($data['link'])) $this->setLink($data['link']); // Берётся из http://search.enter.ru/category/tree (из элемента "children") и http://api.enter.ru/v2/product/get (из элемента "category")
        if (isset($data['url'])) $this->setLink($data['url']); // Берётся из https://scms.enter.ru/category/get/v1, https://scms.enter.ru/category/gets, https://scms.enter.ru/product/get-description и т.п.

        if (isset($data['token'])) $this->setToken($data['token']); // Берётся из http://search.enter.ru/category/tree (из элемента "children") и http://api.enter.ru/v2/product/get (из элемента "category")
        if (isset($data['slug'])) $this->setToken($data['slug']); // Берётся из https://scms.enter.ru/category/get/v1, https://scms.enter.ru/category/gets, https://scms.enter.ru/product/get-description и т.п.

        if (isset($data['media_image'])) $this->image = $data['media_image']; // Возвращается методом http://search.enter.ru/category/tree
        if (isset($data['media_image_480x480'])) $this->image480x480 = $data['media_image_480x480']; // Возвращается методом http://search.enter.ru/category/tree

        // Берётся из https://scms.enter.ru/category/get/v1, https://scms.enter.ru/category/gets
        if (isset($data['medias']) && is_array($data['medias'])) {
            foreach ($data['medias'] as $media) {
                if (is_array($media)) {
                    $this->medias[] = new Media($media);
                }
            }
        }

        if (array_key_exists('children', $data) && is_array($data['children'])) {
            foreach ($data['children'] as $childrenData) {
                $this->addChild(new self($childrenData));
            }
        }

        if (isset($data['product_view_id'])) $this->setProductView($data['product_view_id']);
        if (isset($data['level'])) $this->setLevel($data['level']);

        if (isset($data['title'])) $this->setSeoTitle($templateHelper->unescape($data['title']));
        if (isset($data['meta_keywords'])) $this->setSeoKeywords($templateHelper->unescape($data['meta_keywords']));
        if (isset($data['meta_description'])) $this->setSeoDescription($templateHelper->unescape($data['meta_description']));
        if (isset($data['content'])) $this->setSeoContent($data['content']);

        if (isset($data['property']['seo']['hotlinks'])) {
            $this->setSeoHotlinks($data['property']['seo']['hotlinks']);
        }

        if (isset($data['product_count'])) $this->setProductCount($data['product_count']);
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

        if (isset($data['parent'])) $this->parent = new Entity($data['parent']);

        $this->config = new Config(array_key_exists('config', $data) ? $data['config'] : []);

        $this->listingView = new ListingView();
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
            if (0 == $size) {
                return $this->image;
            } else if (3 == $size) {
                return $this->image480x480;
            }
        } else if ($this->medias) {
            if (0 == $size) {
                return $this->getMediaSource('category_163x163')->url;
            } else if (3 == $size) {
                return $this->getMediaSource('category_480x480')->url;
            }
        }
    }

    /**
     * @param string $sourceType
     * @param string $mediaProvider
     * @param string $mediaTag
     * @return Media\Source
     */
    public function getMediaSource($sourceType, $mediaTag = 'main', $mediaProvider = 'image') {
        foreach ($this->medias as $media) {
            if ($media->provider === $mediaProvider
                && (!$media->tags || in_array($mediaTag, $media->tags, true))
            ) {
                foreach ($media->sources as $source) {
                    if ($source->type === $sourceType) {
                        return $source;
                    }
                }
            }
        }

        return new Media\Source();
    }

    // TODO отрефакторить методы для получения родительских категорий

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
    public function getRootOfParents() {
        $root = $this;
        while ($root->parent) {
            $root = $root->parent;
        }

        return $root;
    }

    /**
     * @return Entity
     */
    public function getRoot() {
        return reset($this->ancestor);
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
     * @param Entity[] $children
     */
    public function setChild(array $children) {
        $this->child = $children;
    }

    /**
     * @return Entity[]
     */
    public function getChild() {
        return $this->child;
    }

    public function isV2Root() {
        return (self::UI_BYTOVAYA_TEHNIKA === $this->getUi()); // Корневая бытовой техники
    }

    public function isV2() {
        return in_array($this->getRootOrSelf()->getUi(), [
            self::UI_BYTOVAYA_TEHNIKA, // Бытовая техника
            self::UI_MEBEL, // Мебель
            self::UI_ELECTRONIKA, // Электронника
            self::UI_TCHIBO
        ], true) || $this->isTyre() || $this->token == 'shop';
    }

    public function isV2Furniture() {
        $root = $this->getRootOrSelf();
        // Мебель
        return $root->getUi() === self::UI_MEBEL;
    }

    public function isShowSmartChoice() {
        $root = $this->getRootOrSelf();

        // Мебель
        return $root->getUi() !== self::UI_MEBEL;
    }

    /**
     * Показывать ли изображения категорий в фильтрах
     * @return bool
     */
    public function isShowFullChildren() {
        if ($this->isV2()) {
            return (bool)$this->getClosestFromAncestors([
                '56ee3e3c-a1ee-4a42-834f-97bd1de3b16e', // Мебель для руководителей
                'da1e9ace-9c81-4d19-a069-36a809e8b98f', // Мебель для персонала
                'df612c33-3a48-47dd-b424-f0398f82e37e', // Коллекции мебели для гостиной
                '7d6fd0b2-e57b-4f4b-a577-9ce81d6af07a', // Коллекции мебели для спальни
                '61b83d8a-6383-4e51-9173-f51f89726cd4', // Коллекции мебели для прихожей
                '4358f982-288f-4973-8eec-d77253fc9233', // Коллекции мебели для детской
                '81dd06df-221c-4eb8-b095-73b3982f0874', // Коллекции мягкой мебели
                self::UI_TCHIBO
            ]);
        }

        return true;
    }

    public function isFakeShopCategory() {
        return $this->token == self::FAKE_SHOP_TOKEN;
    }

    public function isAlwaysShowBrand() {
        if ($this->isV2()) {
            return (bool)$this->getClosestFromAncestors([
                self::UI_BYTOVAYA_TEHNIKA, // Бытовая техника
                self::UI_ELECTRONIKA, // Электронника
            ]) || $this->isFakeShopCategory();
        }

        return false;
    }

    public function isTyre() {
        return in_array($this->getUi(), [
            '94fe0c01-665b-4f66-bb9d-c20e62aa9b7a', // Шины и принадлежности
            '018638bb-b54b-473f-8cb0-fa3953cd3695', // Шины и принадлежности -> Шины
        ], true);
    }

    /**
     * Возвращает ближайшую категорию из родителей по ui
     * @param array $expectedUis
     *
     * @return Entity|null
     */
    private function getClosestFromAncestors(array $expectedUis) {
        /** @var Entity[] $ancestors */
        $ancestors = $this->ancestor;
        $ancestors[] = $this;
        $ancestors = array_reverse($ancestors);

        foreach ($ancestors as $category) {
            if (in_array($category->getUi(), $expectedUis, true)) {
                return $category;
            }
        }

        return null;
    }

    private function getRootOrSelf() {
        $root = $this->getRoot();
        if (!$root) {
            return $this;
        }

        return $root;
    }

    public function isV3() {
        return in_array($this->getUi(), [
            '0dd8ef4e-7eb3-4281-95f3-0cf2f1d469e9', // Raganella princess
            '9cbeabe3-0a06-4368-8e16-1e617fb74d7b', // Браслеты Raganella Princess
            'c61f0526-ad96-41e6-8c83-b49b4cb06a7d', // Колье Raganella Princess
            'd2a5feac-110c-4c08-9d49-b69abf9f8861', // Серьги Raganella Princess

            'd792f833-f6fa-4158-83f6-2ac657077076', // Кольца Бронницкий Ювелир
            '4caf66a4-f1c4-4b79-a6e4-1f2e6a1700cc', // Подвески Бронницкий Ювелир
            'd4bc284a-9a1f-4614-a3d0-ec690d7e1b78', // Серьги Бронницкий Ювелир
            'ae6975b8-f6e3-46b3-baba-a85305213dea', // Цепи Бронницкий Ювелир
            'cd2c06d0-a087-47c2-a043-7ca02317424a', // Танцующие бриллианты

            '633c0d73-d9f5-4984-a679-e8154be71c6a', // ЗОЛОТЫЕ УКРАШЕНИЯ
            '3835654e-0b7c-4ce8-9006-f042fdb9676a', // Золотые серьги
            '152aacd2-b43c-4b48-ac16-a95045ad8083', // Золотые кольца
            '1c0c96a5-6fcb-4b00-9616-8c41fae9f0c0', // Золотые колье и подвески
            '759d26d8-96de-4960-8ca9-8a7a0633ff8c', // Золотые цепи и браслеты
            '5e97747a-31d7-4f4e-9031-3b7122e53b66', // Золотой пирсинг

            '06aaa4e1-1546-4364-a9a5-68d0f9a39fae', // СЕРЕБРЯНЫЕ УКРАШЕНИЯ
            '6e6c8154-5ee6-437a-bb74-644c1b67a096', // Серебряные серьги
            'a6018a60-da37-49f6-b195-58e4a651f914', // Серебряные кольца
            'd869c0e2-958c-4919-b6e8-0f9159b74204', // Серебряные колье и подвески
            '0423bec4-a8a7-4e85-a334-71089a2baf9f', // Серебряные цепи и браслеты

            '5505db94-143c-4c28-adb9-b608d39afe26', // КОЛЬЦА
            'd7b951ed-7b94-4ece-a3ae-c685cf77e0dd', // СЕРЬГИ
            '8b21a199-4c0a-4eba-91e8-6833b4b7a443', // КОЛЬЕ И ПОДВЕСКИ
            'fb4788dd-25fb-49dd-a3a0-9da170b28d70', // ДЕТСКИЕ УКРАШЕНИЯ
            '35386cba-037b-4db1-b3f1-64d5ba2e492a', // Детские серьги
            '3d5785ba-e2bf-4450-a0e1-938b4447dfdb', // Детские кольца
            'a1acc4d6-0a63-411d-ba82-696a9600402f', // Детские цепочки
            '968c7510-d174-434c-8fd5-0a4941280792', // Детские подвески

            '5f80bd78-df8e-4f8f-b8a0-9258479484bd', // УКРАШЕНИЯ ИЗ СЕРЕБРА
            '9a4758ad-bc74-4c3b-a113-dcf76e61c35d', // Кольца из серебра
            'e0b806a4-bd2b-4360-869d-9c078dadd6c3', // Серьги из серебра
            'f2ffa700-0ac7-4125-867b-1a114b5f20b6', // Подвески из серебра
        ], true);
    }

    public function isPandora() {
        return $this->getCategoryClass() === 'jewel';
    }

    /**
     * Является ли категория Чибовской
     * @return bool
     */
    public function isTchibo()
    {
        return array_key_exists(0, $this->ancestor) && $this->ancestor[0]->getUi() === self::UI_TCHIBO;
    }

    /** Ручной гридстер
     * @return bool
     */
    public function isManualGrid() {
        return $this->config->isManualGridView();
    }

    /** Автоматический гридстер
     * @return bool
     */
    public function isAutoGrid() {
        return $this->config->isAutoGridView();
    }

    public function isDefault() {
        return ($this->getCategoryClass() === 'default' || $this->getCategoryClass() == '');
    }

    public function getCategoryClass() {
        return !empty($this->catalogJson['category_class']) ? strtolower(trim((string)$this->catalogJson['category_class'])) : null;
    }

    /**
     * SITE-5772
     * @return array
     */
    public function getSenderForGoogleAnalytics() {
        if ($this->isPandora()) {
            $sender = ['name' => 'filter_pandora'];
        } else if ($this->isV3()) {
            $sender = ['name' => 'filter_jewelry'];
        } else if ($this->isV2()) {
            $sender = ['name' => 'filter'];
        } else if ($this->isDefault()) {
            $sender = ['name' => 'filter_old'];
        } else {
            $sender = [];
        }

        if ($sender) {
            $sender['categoryUrlPrefix'] = $this->getUrlPrefix();
        }

        return $sender;
    }

    private function getUrlPrefix() {
        if (preg_match('/^\/catalog\/([^\/]*).*$/i', parse_url($this->link, PHP_URL_PATH), $matches)) {
            return $matches[1];
        }

        return '';
    }

    private function convertCatalogJsonToOldFormat($data) {
        $result = [];

        if (is_array($data)) {
            if (isset($data['uid'])) {
                $result['ui'] = $data['uid'];
            }

            if (isset($data['property']['bannerPlaceholder'])) {
                $result['bannerPlaceholder'] = $data['property']['bannerPlaceholder'];

                if (isset($result['bannerPlaceholder']['image'])) {
                    $result['bannerPlaceholder']['image'] = trim($result['bannerPlaceholder']['image']);
                }

                if (isset($result['bannerPlaceholder']['url'])) {
                    $result['bannerPlaceholder']['url'] = trim($result['bannerPlaceholder']['url']);
                }
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

class ListingView {
    /** @var bool */
    public $isList = false;
    /** @var bool */
    public $isMosaic = true;
}