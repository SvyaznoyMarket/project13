<?php

/**
 * ProductCategoryFilterEntity repository
 */
class ProductCategoryFilterRepository
{
  /**
   * @param $categoryId
   * @return ProductCategoryFilterEntity[]
   */
  public function getList($categoryId)
  {
    $response = CoreClient::getInstance()->query('listing.filter', array(
      'region_id' => (int)RepositoryManager::getRegion()->getDefaultRegionId(),
      'category_id' => (int)$categoryId,
    ));
    $list = array();
    foreach ($response as $item) {
      $list[] = new ProductCategoryFilterEntity($item);
    }
    return $list;
  }

  /**
   * Example of asynchronous query
   *
   * <code>
   * $repo->getListAsync(127, function($filterList){
   *    foreach($filterList as $filter) {
   *      $filter; // ProductCategoryFilterEntity
   *    }
   * });
   * CoreClient::getInstance()->execute();
   * // there all queue queries is loaded
   * </code>
   *
   * @see CoreClient::execute
   * @see CoreClient::addQuery
   * @param $categoryId
   * @param callback $callback First parameter is ProductCategoryFilterEntity[]
   */
  public function getListAsync($categoryId, $callback)
  {
    CoreClient::getInstance()->addQuery('listing.filter', array(
      'region_id' => (int)RepositoryManager::getRegion()->getDefaultRegionId(),
      'category_id' => (int)$categoryId,
    ), array(), function(array $response) use ($callback)
    {
      $list = array();
      foreach ($response as $item) {
        $list[] = new ProductCategoryFilterEntity($item);
      }
      $callback($list);
    });
  }
}
