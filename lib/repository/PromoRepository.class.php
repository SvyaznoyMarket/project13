<?php

/**
 * PromoEntity repository
 */
class PromoRepository
{
  private $routing = null;
  private $productRepository = null;

  public function __construct() {
    $this->routing = sfContext::getInstance()->getRouting();
    $this->productRepository = RepositoryManager::getProduct();
    $this->productCategoryRepository = RepositoryManager::getProductCategory();
  }

  public function get($type = null) {
    $params = array();
    if ($type) {
      $params['type_id'] = $type;
    }

    $indexById = function(array $array) {
      $return = array();
      foreach ($array as $item) {
        $return[$item->getId()] = $item;
      }

      return $return;
    };

    $result = CoreClient::getInstance()->query('promo/get', $params);

    $itemObjectIdsByType = array(
      PromoItemEntity::TYPE_PRODUCT          => array(),
      PromoItemEntity::TYPE_PRODUCT_CATEGORY => array(),
    ); // товары, категории товаров, ...
    foreach($result as $promoData) {
      foreach ($promoData['item_list'] as $itemData) {
        switch ($itemData['type_id'])
        {
          case PromoItemEntity::TYPE_PRODUCT:
            $itemObjectIdsByType[PromoItemEntity::TYPE_PRODUCT][] = $itemData['id'];
            break;
          case PromoItemEntity::TYPE_PRODUCT_CATEGORY:
            $itemObjectIdsByType[PromoItemEntity::TYPE_PRODUCT_CATEGORY][] = $itemData['id'];
            break;
        }
      }
    }

    $itemObjectsByType = array(
      PromoItemEntity::TYPE_PRODUCT          => $indexById($this->productRepository->getListById(array_unique($itemObjectIdsByType[PromoItemEntity::TYPE_PRODUCT]))),
      PromoItemEntity::TYPE_PRODUCT_CATEGORY => $indexById($this->productCategoryRepository->getListById(array_unique($itemObjectIdsByType[PromoItemEntity::TYPE_PRODUCT_CATEGORY]))),
    );

    $return = array();
    foreach($result as $promoData) {
      foreach ($promoData['item_list'] as $i => $itemData) {
        $promoData['item_list'][$i]['object'] = array_key_exists($itemData['id'], $itemObjectsByType[$itemData['type_id']]) ? $itemObjectsByType[$itemData['type_id']][$itemData['id']] : null;
      }

      $promo = new PromoEntity($promoData);
      $promo->setUrl($this->generateEntityUrl($promo));

      $return[] = $promo;
    }

    return $return;
  }

  private function generateEntityUrl($entity)
  {
    $return = '#';

    $entityUrl = $entity->getUrl();
    if (!empty($entityUrl)) {
      $return = $entityUrl;
    }
    else if (!count($entity->getItems()) && !$entity->isDummy()) {
      $return = null;
    }
    else if (count($entity->getItems()) == 1) {
      list($promoItem) = $entity->getItems();
      if ($promoItem->getObject()) {
        switch ($promoItem->getType())
        {
          case PromoItemEntity::TYPE_PRODUCT:
            $return = $promoItem->getObject()->getLink();
            break;
          case PromoItemEntity::TYPE_PRODUCT_CATEGORY:
            $return = $promoItem->getObject()->getLink();
            break;
        }
      }
    }
    // for product's set
    else if (count($entity->getItems()) > 1) {
      $barcodes = array();
      foreach ($entity->getItems() as $item) {
        /* @var $item PromoItemEntity */
        if (PromoItemEntity::TYPE_PRODUCT != $item->getType() || !$item->getObject()) continue;

        $barcodes[] = $item->getObject()->getBarcode();
      }

      if (count($barcodes)) {
        $return = $this->routing->generate('product_set', array('products' => implode(',', $barcodes)), true);
      }
    }

    return $return;
  }
}