<?php
class SimilarProductTable extends myDoctrineTable
{

  public static function getInstance()
  {
      return Doctrine_Core::getTable('SimilarProduct');
  }

  public function getListByProduct(Product $product, array $params = array())
  {
    $this->applyDefaultParameters($params);
    $productTable = ProductTable::getInstance();

    $q = $productTable->createBaseQuery($params);

    $q->innerJoin('product.MasterSimilarProduct masterProduct')
      ->addWhere('masterProduct.master_id = ?', $product->id)
      ->orderBy('product.rating DESC')
      //->useResultCache(true, null, $this->getQueryHash("product-{$product->id}/productComment-all", $params))
    ;

    $this->setQueryParameters($q, $params);

    $ids = $productTable->getIdsByQuery($q);

    return $productTable->createListByIds($ids, $params);
  }
}