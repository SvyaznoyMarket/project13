<?php

/**
 * productCategory components.
 *
 * @package    enter
 * @subpackage productCategory
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCategory_Components extends myComponents
{

  /**
   * Executes root_list component
   *
   * @param ProductCategory $productCategory Текущая категория товара
   */
  public function executeRoot_list()
  {
    //    $list = array();
    //    foreach (ProductCategoryTable::getInstance()->getRootList() as $productCategory)
    //    {
    //      $list[] = array(
    //        'name' => (string)$productCategory,
    //        'url'  => $this->generateUrl('productCatalog_category', $productCategory),
    //      );
    //    }

    $list = RepositoryManager::getProductCategory()->getTree(null, 1);

    foreach($list as $key => $category){
      if(!$category->getIsShownInMenu()){
        unset($list[$key]);
      }
    }

    $this->setVar('list', $list, true);
  }
}
