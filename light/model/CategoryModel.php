<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 15.05.12
 * Time: 12:51
 * To change this template use File | Settings | File Templates.
 */
require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');


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
    $data = App::getCoreV2()->query('category.tree', array(
      'root_id' => $categoryId,
      'max_level' => $maxLevel,
      'is_load_parents' => $loadParents,
      'region_id' => App::getCurrentUser()->getRegion()->getId(),
    ));

    return $data;
  }

}
