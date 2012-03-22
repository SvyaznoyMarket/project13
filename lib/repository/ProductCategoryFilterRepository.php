<?php

/**
 * ProductCategoryFilterEntity repository
 */
class ProductCategoryFilterRepository
{
  /**
   * @param $regionId
   * @param $categoryId
   * @return ProductCategoryFilterEntity[]
   */
  public function getList($regionId,$categoryId){
    $response = CoreClient::getInstance()->query('listing.filter', array(
      'region_id' => (int)$regionId,
      'category_id' => (int)$categoryId,
    ));
    $list = array();
    foreach($response as $item){
      $list[] = new ProductCategoryFilterEntity($item);
    }
    return $list;
  }

  /**
   * Example of asynchronous query
   *
   * @see CoreClient::execute
   * @see CoreClient::addQuery
   * @param $regionId
   * @param $categoryId
   * @param callback $callback First parameter is ProductCategoryFilterEntity[]
   */
  public function getListAsync($regionId, $categoryId, $callback){
    CoreClient::getInstance()->addQuery('listing.filter', array(
      'region_id' => (int)$regionId,
      'category_id' => (int)$categoryId,
    ), array(), function(array $response) use ($callback) {
      $list = array();
      foreach($response as $item){
        $list[] = new ProductCategoryFilterEntity($item);
      }
      $callback($list);
    });
  }
}
