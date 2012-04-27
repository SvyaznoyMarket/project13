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
  public function getListByCategory(array $categoryList, array $filters = array(), array $sort = array(), $offset = null, $limit = null)
  {
    $productRepo = RepositoryManager::getProduct();
    /** @var $list ProductCategoryTagView[] */
    $list = array();
    $filterList = array();
    foreach ($categoryList as $category) {
      $list[] = $tag = new ProductCategoryTagView();
      $filterList[] = array(
        'limit' => $limit,
        'offset' => $offset,
        'filters' => array_merge(
          array(array('category', 1, $category->getId())),
          $filters
        ),
        'sort' => $sort,
      );
      $tag->category = $category;
    }
    if ($filterList) {
      $idList = array();
      $data = RepositoryManager::getListing()->getListingMultiple($filterList);
      // clear list
      foreach ($data as $key => $listingData) {
        $tag = $list[$key];
        $tag->productCount = (int)$listingData['count'];
        if ($tag->productCount) {
          $tag->productList = array_flip($listingData['list']);
          $idList = array_merge($idList, $listingData['list']);
        }
        else
          unset($list[$key]);
      }
      if ($idList)
        foreach ($productRepo->getListById($idList, true) as $entity)
          /** @var $tag ProductCategoryTagView */
          foreach ($list as $tag)
            if (array_key_exists($entity->getId(), $tag->productList))
              $tag->productList[$entity->getId()] = $entity;
    }
    return $list;
  }
}
