<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model as ObjectModel;

class Product {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $article;
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
    /** @var ObjectModel\Product\Category|null */
    public $category;
    /** @var ObjectModel\Brand|null */
    public $brand;
    /** @var ObjectModel\Product\Property[] */
    public $properties = [];
    /** @var ObjectModel\Product\Property\Group[] */
    public $propertyGroups = [];
    /** @var ObjectModel\Product\Stock[] */
    public $stock = [];
    /** @var int */
    public $price;
    /** @var int */
    public $oldPrice;
    /** @var ObjectModel\Product\Media */
    public $media;
    /** @var ObjectModel\Product\Rating|null */
    public $rating;
    /** @var ObjectModel\Product\Model|null */
    public $model;
    /** @var ObjectModel\Product\NearestDelivery[] */
    public $nearestDeliveries = [];
    /** @var string[] */
    public $accessoryIds = [];
    /** @var string[] */
    public $relatedIds = [];
    /** @var ObjectModel\Product\Relation */
    public $relation;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->media = new ObjectModel\Product\Media();
        $this->relation = new ObjectModel\Product\Relation();

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
        if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
        if (array_key_exists('link', $data)) $this->link = rtrim((string)$data['link'], '/');
        if (array_key_exists('description', $data)) $this->description = (string)$data['description'];
        if (array_key_exists('price', $data)) $this->price = $data['price'] ? (int)$data['price'] : null;
        if (array_key_exists('price_old', $data)) $this->oldPrice = $data['price_old'] ? (int)$data['price_old'] : null;

        $this->isBuyable = isset($data['state']['is_buyable']) && (bool)$data['state']['is_buyable'];
        $this->calculateState(isset($data['stock'][0]) ? $data['stock'] : []);

        if (isset($data['category'][0])) {
            $categoryItem = (array)array_pop($data['category']);
            $this->category = new ObjectModel\Product\Category($categoryItem);

            foreach ($data['category'] as $categoryItem) {
                $this->category->ascendants[] = new ObjectModel\Product\Category($categoryItem);
            }
        }

        if (isset($data['brand']['id'])) $this->brand = new ObjectModel\Brand($data['brand']);

        if (isset($data['property'][0])) {
            foreach ($data['property'] as $propertyItem) {
                $this->properties[] = new ObjectModel\Product\Property($propertyItem);
            }
        }

        if (isset($data['property_group'][0])) {
            foreach ($data['property_group'] as $propertyGroupItem) {
                $this->propertyGroups[] = new ObjectModel\Product\Property\Group($propertyGroupItem);
            }
        }

        if (isset($data['media'][0])) {
            foreach ($data['media'] as $mediaItem) {
                $this->media->photos[] = new ObjectModel\Product\Media\Photo($mediaItem);
            }
        }

        if (isset($data['stock'][0])) {
            foreach ($data['stock'] as $stockItem) {
                $this->stock[] = new ObjectModel\Product\Stock($stockItem);
            }
        }

        if (isset($data['model']['property'][0])) $this->model = new ObjectModel\Product\Model($data['model']);
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