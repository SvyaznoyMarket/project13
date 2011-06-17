<?php

/**
 * productHelper components.
 *
 * @package    enter
 * @subpackage productHelper
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productHelperComponents extends myComponents
{
 /**
  * Executes list component
  *
  * @param myDoctrineCollection $productHelperList Коллекция помошников по выбору товара
  */
  public function executeList()
  {
    $list = array();
    foreach ($this->productHelperList as $productHelper)
    {
      $list[] = array(
        'name' => $productHelper->name,
        'url'  => url_for('productHelper_show', $productHelper),
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes filter component
  *
  * @param ProductHelper $productHelper Помошник по выбору товара
  * @param myProductHelperFormFilter $productHelperFilter форма фильтра
  */
  public function executeFilter()
  {
    if (empty($this->productHelperFilter))
    {
      $this->productHelperFilter = new myProductHelperFormFilter(array(), array(
        'productHelper' => $this->productHelper,
      ));
    }
  }
}
