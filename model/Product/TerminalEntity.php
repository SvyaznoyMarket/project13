<?php

namespace Model\Product;

class TerminalEntity extends Entity {
    /** @var bool */
    protected $isBuyable = null;
    /** @var bool */
    protected $isInShop = null;
    /** @var bool */
    protected $isInShopShowroom = null;

    /**
     * @param int $shopId
     * @return bool
     */
    public function getIsBuyable($shopId) {
        $shopId = (int)$shopId;
        if (!$shopId) return false;

        if (!is_null($this->isBuyable)) {
            return $this->isBuyable;
        }

        $this->isBuyable = $this->getState()->getIsStore() || $this->getState()->getIsSupplier();
        foreach ($this->getStock() as $stock) {
            if ($stock->getShopId() == $shopId) {
                $this->isBuyable |= ($stock->getQuantity() > 0 || $stock->getQuantityShowroom() > 0);
            }
        }
        return $this->isBuyable;
    }

    /**
     * @param int $shopId
     * @return bool
     */
    public function getIsInShop($shopId) {
        $shopId = (int)$shopId;
        if (!$shopId) return false;

        if (!is_null($this->isInShop)) {
            return $this->isInShop;
        }

        $this->isInShop = false;
        foreach ($this->getStock() as $stock) {
            if ($stock->getShopId() == $shopId) {
                $this->isInShop = $stock->getQuantity() > 0;
                break;
            }
        }

        return $this->isInShop;
    }

    /**
     * @param int $shopId
     * @return bool
     */
    public function getIsInShowroom($shopId) {
        $shopId = (int)$shopId;
        if (!$shopId) return false;

        if (!is_null($this->isInShopShowroom)) {
            return $this->isInShopShowroom;
        }

        $this->isInShopShowroom = false;
        foreach ($this->getStock() as $stock) {
            if ($stock->getShopId() == $shopId) {
                $this->isInShopShowroom = $stock->getQuantityShowroom() > 0;
                break;
            }
        }

        return $this->isInShopShowroom;
    }
}