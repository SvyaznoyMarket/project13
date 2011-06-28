<?php
class SimilarProductTable extends myDoctrineTable
{

  public static function getInstance()
  {
      return Doctrine_Core::getTable('SimilarProduct');
  }

  public function createBaseQuery(array $params = array())
  {
    $this->applyDefaultParameters($params);

    $q = $this->createQuery('product');

    $q
      ->orderBy('product.rating DESC')
    ;

    return $q;
  }

  public function getListByProduct(Product $product, array $params = array())
  {
    $this->applyDefaultParameters($params);

    $q = $this->createBaseQuery($params);

    $q->leftJoin('product.MasterSimilarProduct masterSimilarProduct')
      ->addWhere('masterSimilarProduct.master_id = ?', $product->id)
      //->useResultCache(true, null, $this->getQueryHash("product-{$product->id}/productComment-all", $params))
    ;

    $this->setQueryParameters($q, $params);

    $ids = $this->getIdsByQuery($q);

    return $this->createListByIds($ids, $params);
  }
}