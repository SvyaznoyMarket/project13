<?php

class ProductCategoryTagViewRepository
{
  /**
   * @param ProductCategoryEntity[] $categoryList
   * @param array $filters
   * @param array $sort
   * @param int $offset
   * @param int $limit
   * @return ProductCategoryTagView[]
   */
  public function getListByCategory(array $categoryList, $filters = array(), $sort = array(), $offset = null, $limit = null)
  {
    $listingRepo = RepositoryManager::getListing();
    $productRepo = RepositoryManager::getProduct();
    $list = array();
    $idList = array();
    foreach ($categoryList as $category)
    {
      $list[] = $tag = new ProductCategoryTagView();
      $tag->category = $category;
      $listingRepo->getListingAsync(function($data) use($tag, &$idList)
      {
        /** @var $tag ProductCategoryTagView */
        $tag->productCount = $data['count'];
        $tag->productList = array_flip($data['list']);
        $idList = array_merge($idList, $data['list']);
      }, $filters, $sort, $offset, $limit);
    }
    CoreClient::getInstance()->execute();
    foreach ($productRepo->getListById($idList, true) as $entity) {
      /** @var $view ProductCategoryTagView */
      foreach ($list as $view) {
        if (array_key_exists($entity->getId(), $view->productList)) {
          $view->productList[$entity->getId()] = $entity;
        }
      }
    }
    return $list;
  }
}
