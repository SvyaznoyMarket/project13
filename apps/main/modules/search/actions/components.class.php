<?php

/**
 * search components.
 *
 * @package    enter
 * @subpackage search
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @property string               $searchString Поисковая фраза
 * @property myDoctrineCollection $productTypeList Коллекция типов товаров
 * @property ProductType          $productType     Выбранный тип товара
 */
class searchComponents extends myComponents
{
 /**
  * Executes navigation component
  *
  *
  */
  public function executeNavigation()
  {
    $list = array();

    $list[] = array(
      'name' => "Поиск (".$this->searchString.")",
      'url'  => $this->generateUrl('search', array('searchString' => $this->searchString)),
    );

    $this->setVar('list', $list, false);
  }

  public function executeFilter_productType()
  {
    $list = array(
      'first' => array(),
      'other' => array(),
    );

    if(!$this->productTypeList){
      return sfView::NONE;
    }
    $this->productTypeList->loadRelated('ProductCategory');
    $firstProductCategory = isset($this->productTypeList[0]->ProductCategory[0]) ?
      $this->productTypeList[0]->ProductCategory[0]->getRootCategory()
    : new ProductCategory();
    foreach ($this->productTypeList as $i => $productType)
    {
      $index = 'other';
      if ($firstProductCategory)
      {
        /** @var $productCategory ProductCategory */
        foreach ($productType->ProductCategory as $productCategory)
        {
          if ($productCategory->getRootCategory()->id == $firstProductCategory->id)
          {
            $index = 'first';
            break;
          }
        }
      }

      $list[$index][] = array(
        'url'      => $this->generateUrl('search', array('q' => $this->searchString, 'product_type' => $productType->id)),
        'name'     => (string)$productType,
        'token'    => $productType->id,
        'count'    => isset($productType->_product_count) ? $productType->_product_count : 0,
        'value'    => $productType->id,
        'selected' => ((0 == $i) && !$this->productType) || ($this->productType && ($this->productType->id == $productType->id)),
      );
    }

    $this->setVar('list', $list, true);
    $this->setVar('firstProductCategory', $firstProductCategory, true);

    return sfView::SUCCESS;
  }
}
