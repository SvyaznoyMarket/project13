<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Product {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $article;
    /** @var string */
    public $webName;
    /** @var string */
    public $namePrefix;
    /** @var string */
    public $name;
    /** @var string */
    public $token;
    /** @var string */
    public $link;
    /** @var string */
    public $description;
    /** @var bool */
    public $isBuyable;
    /** @var bool */
    public $isInShopOnly;
    /** @var bool */
    public $isInShopStockOnly;
    /** @var bool */
    public $isInShopShowroomOnly;
    /** @var Model\Product\Category|null */
    public $category;
    /** @var Model\Brand|null */
    public $brand;
    /** @var Model\Product\Property[] */
    public $properties = [];
    /** @var Model\Product\Property\Group[] */
    public $propertyGroups = [];
    /** @var Model\Product\Stock[] */
    public $stock = [];
    /** @var int */
    public $price;
    /** @var int */
    public $oldPrice;
    /** @var Model\Product\Media */
    public $media;
    /** @var Model\Product\Rating|null */
    public $rating;
    /** @var Model\Product\ProductModel|null */
    public $model;
    /** @var Model\Product\NearestDelivery[] */
    public $nearestDeliveries = [];
    /** @var string[] */
    public $accessoryIds = [];
    /** @var string[] */
    public $relatedIds = [];
    /** @var Model\Product\Relation */
    public $relation;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->media = new Model\Product\Media();
        $this->relation = new Model\Product\Relation();

        if ((bool)$data) {
            $this->import($data);
        }
    }

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('article', $data)) $this->article = (string)$data['article'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('prefix', $data)) $this->namePrefix = (string)$data['prefix'];
        if (array_key_exists('name_web', $data)) $this->webName = (string)$data['name_web'];
        if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
        if (array_key_exists('link', $data)) $this->link = rtrim((string)$data['link'], '/');
        if (array_key_exists('description', $data)) $this->description = (string)$data['description'];
        if (array_key_exists('price', $data)) $this->price = $data['price'] ? (int)$data['price'] : null;
        if (array_key_exists('price_old', $data)) $this->oldPrice = $data['price_old'] ? (int)$data['price_old'] : null;

        $this->isBuyable = isset($data['state']['is_buyable']) && (bool)$data['state']['is_buyable'];
        $this->calculateState(isset($data['stock'][0]) ? $data['stock'] : []);

        if (isset($data['category'][0])) {
            $categoryItem = (array)array_pop($data['category']);
            $this->category = new Model\Product\Category($categoryItem);

            foreach ($data['category'] as $categoryItem) {
                $this->category->ascendants[] = new Model\Product\Category($categoryItem);
            }
        }

        if (isset($data['brand']['id'])) $this->brand = new Model\Brand($data['brand']);

        if (isset($data['property'][0])) {
            foreach ($data['property'] as $propertyItem) {
                $this->properties[] = new Model\Product\Property((array)$propertyItem);
            }
        }

        if (isset($data['property_group'][0])) {
            foreach ($data['property_group'] as $propertyGroupItem) {
                $this->propertyGroups[] = new Model\Product\Property\Group((array)$propertyGroupItem);
            }
        }

        if (isset($data['media'][0])) {
            foreach ($data['media'] as $mediaItem) {
                if (!isset($mediaItem['id'])) continue;
                $this->media->photos[] = new Model\Product\Media\Photo($mediaItem);
            }
        }

        if (isset($data['stock'][0])) {
            foreach ($data['stock'] as $stockItem) {
                $this->stock[] = new Model\Product\Stock((array)$stockItem);
            }
        }

        if (isset($data['model']['property'][0])) $this->model = new Model\Product\ProductModel($data['model']);
        if (isset($data['accessories'][0])) $this->accessoryIds = $data['accessories'];
        if (isset($data['related'][0])) $this->relatedIds = $data['related'];
    }

    /**
     * @param array $stockData
     */
    protected function calculateState(array $stockData) {
        $inStore = false;
        $inShowroom = false;
        $inShop = false;

        foreach ($stockData as $stockItem) {
            if ($stockItem['store_id']) {
                $inStore = true;
            }
            if ($stockItem['shop_id'] && $stockItem['quantity']) { // есть на складе магазина
                $inShop = true;
            }
            if ($stockItem['shop_id'] && $stockItem['quantity_showroom']) { // есть на витрине магазина
                $inShowroom = true;
            }
        }

        $this->isInShopStockOnly = !$inStore && $inShop && !$inShowroom; // не на центральном складе, на складе магазина, не на витрине магазина
        $this->isInShopShowroomOnly = !$inStore && !$inShop && $inShowroom; // не на центральном складе, не на складе магазина, на витрине магазина
        $this->isInShopOnly = $this->isInShopStockOnly || $this->isInShopShowroomOnly;
    }
}