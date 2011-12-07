<?php

class DeliveryCalc
{
  public static function getShopListForSelfDelivery()
  {
    $haveInStock = false;
    $cart = sfContext::getInstance()->getUser()->getCart()->getProducts();
    $stockRel = StockProductRelationTable::getInstance();
    foreach ($cart as $product_id => $product)
    {
      // stock_id = 1 HARDCODE 
      if (!$stockRel->isInStock($product_id, false, 1, $product['cart']['quantity'])) {
        $haveInStock = false;
      }
    }
    if ($haveInStock === true) {
      return ShopTable::getInstance()->getChoices();
    } else {
      $shops = ShopTable::getInstance()->getChoices();
      foreach ($cart as $product_id => $product)
      {
        $tmpshops = array();
        $q = ShopTable::getInstance()->createBaseQuery();
        $q->innerJoin('shop.ProductRelation productRelation');
        $q->andWhere('productRelation.product_id = ?', (int)$product_id);
        $q->andWhere('productRelation.stock_id IS NULL');
        $q->andWhere('productRelation.quantity >= ?', $product['cart']['quantity']);
        $q->andWhere('shop.region_id = ?', sfContext::getInstance()->getUser()->getRegion('id'));
        $data = $q->fetchArray();
        foreach ($data as $row) {
            $tmpshops[$row['id']] = $row['name'];
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
    foreach ($cart as $product_id => $product)
    {
        if (StockProductRelationTable::getInstance()->isInStock($product_id, $shop_id, null, $product['cart']['quantity'])) {
          if (time() > $ts) {
            $ts = time();
          }
        } elseif (StockProductRelationTable::getInstance()->isInStock($product_id, false, null, $product['cart']['quantity'])) {
          if (strtotime('tomorrow') > $ts) {
            $ts = strtotime('tomorrow');
          }
        }
    }
    if ($returnDiff) {
      $minDeliveryDate = DateTime::createFromFormat('Y-m-d', date('Y-m-d', $ts));
      $now = new DateTime();
      return $minDeliveryDate->diff($now)->days;
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