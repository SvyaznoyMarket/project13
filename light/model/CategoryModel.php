<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 15.05.12
 * Time: 12:51
 * To change this template use File | Settings | File Templates.
 */
require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
require_once(VIEW_PATH.'dataObject/CategoryShortData.php');

class CategoryModel
{

  /**
   * @param int $categoryId core category id
   * @param int $maxLevel
   * @param bool $loadParents
   * @return ProductCategoryEntity[]
   */
  public function getTreeAsArray($categoryId, $maxLevel = null, $loadParents = false)
  {
    TimeDebug::start('CategoryModel:getTreeAsArray:clientV2');
    $data = App::getCoreV2()->query('category.tree', array(
      'root_id' => $categoryId,
      'max_level' => $maxLevel,
      'is_load_parents' => $loadParents,
      'region_id' => App::getCurrentUser()->getRegion()->getId(),
    ));
    TimeDebug::end('CategoryModel:getTreeAsArray:clientV2');
    return $data;
  }

  /**
   * @param int[] $categoryIdList
   * @return string[]
   */
  public function getUrlsByIdList($categoryIdList){
    $idList = array();
    foreach($categoryIdList as $id){
      $idList[] = (int) $id;
    }
    $idList = array_unique($idList);
    if(count($idList) < 1){
      return array();
    }

    TimeDebug::start('CategoryModel:getBarCodesByIdList:clientV1');
    $data = App::getCoreV1()->query('product.category.get', array(
      'count'   => false,
      'expand'  => array(),
      'limit'   => '',
      'start'   => '',
      'id'      => $idList
    ), array());
    TimeDebug::end('CategoryModel:getBarCodesByIdList:clientV1');

    $return = array();

    foreach ( $data as $category){
      $id = (int) $category['id'];
      $return[$id] = $category['link'];
    }

    return $return;
  }

  /**
   * @return CategoryShortData[]
   *
   * ordered by position
   */
  public function getRootCategoryList(){
    TimeDebug::start('CategoryModel:getRootCategoryList:clientV2');
    $data = App::getCoreV2()->query('category.tree', array(
      'max_level' => 1,
      'is_load_parents' => false,
    ));
    TimeDebug::end('CategoryModel:getRootCategoryList:clientV2');

    $return = array();

    foreach($data as $categoryArray){
      $category = new CategoryShortData();
      $category->setId($categoryArray['id']);
      $category->setLink($categoryArray['link']);
      $category->setName($categoryArray['name']);
      $category->setPosition($categoryArray['position']);
      $category->setToken($categoryArray['token']);
      $return[$categoryArray['position']] = $category;
    }

    return array_values($return);
  }
}
