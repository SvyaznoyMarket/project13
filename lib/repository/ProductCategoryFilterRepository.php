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
}
