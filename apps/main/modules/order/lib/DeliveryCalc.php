<?php

class DeliveryCalc
{
  /**
   *
   * @param int $product_id
   * @param int|null $shop_id
   * @param int $quantity кол-во
   * @return bool
   */
  public function isInStockShop($product_id, $shop_id = null, $quantity = 1)
  {
    $q = ShopTable::getInstance()->createBaseQuery();
    $q->innerJoin('shop.ProductRelation productRelation');
    $q->andWhere('productRelation.product_id = ?', (int)$product_id);
    $q->andWhere('productRelation.stock_id IS NULL');
    $q->andWhere('productRelation.quantity >= ?', $quantity);
    if ($shop_id !== null) {
      $q->andWhere('shop.id = ?', (int)$shop_id);
    } else {
      $q->andWhere('shop.region_id = ?', sfContext::getInstance()->getUser()->getRegion('id'));
    }
    if ($q->count() > 0) {
      return true;
    }
    return false;
  }

  /**
   *
   * @param int $product_id
   * @param int|null $stock_id
   * @param int $quantity кол-во
   * @return bool
   */
  public function isInStockStore($product_id, $stock_id = null, $quantity = 1)
  {
    $q = StockProductRelationTable::getInstance()->createBaseQuery();
    if ($stock_id !== null) {
      $q->andWhere('stockProductRelation.stock_id = ?', (int)$stock_id);
    } else {
      $q->innerjoin('stockProductRelation.Stock stock');
      $q->innerJoin('stock.Region region WITH region.id = ?', sfContext::getInstance()->getUser()->getRegion('id'));
    }
    $q->andWhere('stockProductRelation.product_id = ?', (int)$product_id);
    $q->andWhere('stockProductRelation.shop_id IS NULL');
    $q->andWhere('stockProductRelation.quantity >= ?', $quantity);
    if ($q->count() > 0) {
      return true;
    }
    return false;
  }

  public static function getShopListForSelfDelivery()
  {
    $haveInStock = true;
    $cart = sfContext::getInstance()->getUser()->getCart()->getProducts();
    $region =  sfContext::getInstance()->getUser()->getRegion();
    $stockRel = StockProductRelationTable::getInstance();
    $productsInStore = array();
    foreach ($cart as $product_id => $product)
    {
      // stock_id = 1 HARDCODE
      if (!$stockRel->isInStock($product_id, false, 1, $product['cart']['quantity'])) {
        $haveInStock = false;
        $productsInStore[$product_id] = false;
      } else {
        $productsInStore[$product_id] = true;
      }
    }
    if ($haveInStock === true) {
      return ShopTable::getInstance()->getChoices('id', 'name', array('region_id' => $region['id'], ));
    } else {
      $shops = ShopTable::getInstance()->getChoices('id', 'name', array('region_id' => $region['id'], ));
      foreach ($cart as $product_id => $product)
      {
        if ($productsInStore[$product_id] === true) {
          $tmpshops = $shops;
        } else {
          $tmpshops = array();
          $q = ShopTable::getInstance()->createBaseQuery();
          $q->innerJoin('shop.ProductRelation productRelation');
          $q->andWhere('productRelation.product_id = ?', (int)$product_id);
          $q->andWhere('productRelation.stock_id IS NULL');
          $q->andWhere('productRelation.quantity >= ?', $product['cart']['quantity']);
          $q->andWhere('shop.region_id = ?', $region['id']);
          $data = $q->fetchArray();
          foreach ($data as $row) {
              $tmpshops[$row['id']] = $row['name'];
          }
        }
        $shops = array_intersect_key($shops, $tmpshops);
        if (!count($shops)) {
          break;
        }
      }
      return $shops;
    }
  }

  public static function getMinDateForShopSelfDelivery($shop_id, $returnDiff = false)
  {
    $cart = sfContext::getInstance()->getUser()->getCart()->getProducts();
    $stockRel = StockProductRelationTable::getInstance();
    $ts = time();
    $diff = 0;
    foreach ($cart as $product_id => $product)
    {
        if (StockProductRelationTable::getInstance()->isInStock($product_id, $shop_id, null, $product['cart']['quantity'])) {
          if (time() > $ts) {
            $ts = time();
            $diff = 0;
          }
        } elseif (StockProductRelationTable::getInstance()->isInStock($product_id, false, null, $product['cart']['quantity'])) {
          if (strtotime('tomorrow') > $ts) {
            $ts = strtotime('tomorrow');
            $diff = 1;
          }
        }
    }
    if ($returnDiff) {
//      $minDeliveryDate = DateTime::createFromFormat('Y-m-d', date('Y-m-d', $ts));
//      $now = new DateTime();
//      return $minDeliveryDate->diff($now)->days;
      return $diff;
    } else {
      return date('Y-m-d', $ts);
    }
  }

  public function checkDeliveryForProduct($product_id)
  {
    if (StockProductRelationTable::getInstance()->isInStock($product_id, null, false, 1)) {
      return date('Y-m-d');
    }
    if (StockProductRelationTable::getInstance()->isInStock($product_id, false, null, 1)) {
      return date('Y-m-d', strtotime('tomorrow'));
    }
  }
}