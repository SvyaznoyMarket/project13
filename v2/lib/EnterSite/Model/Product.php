<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Product {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $token;
    /** @var string */
    public $link;
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
    /** @var Model\Product\Media */
    public $media;
    /** @var Model\Product\Rating|null */
    public $rating;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->media = new Model\Product\Media();

        if ((bool)$data) {
            $this->import($data);
        }
    }

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
        if (array_key_exists('link', $data)) $this->link = rtrim((string)$data['link'], '/');

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
                $this->properties[] = new Model\Product\Property($propertyItem);
            }
        }

        if (isset($data['property_group'][0])) {
            foreach ($data['property_group'] as $propertyGroupItem) {
                $this->propertyGroups[] = new Model\Product\Property\Group($propertyGroupItem);
            }
        }

        if (isset($data['media'][0])) {
            foreach ($data['media'] as $mediaItem) {
                $this->media->photos[] = new Model\Product\Media\Photo($mediaItem);
            }
        }
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