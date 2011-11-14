<?php

/**
 * search components.
 *
 * @package    enter
 * @subpackage search
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class searchComponents extends myComponents
{
 /**
  * Executes form component
  *
  * @param string $searchString Поисковая фраза
  */
  public function executeForm()
  {
    if (!in_array($this->view, array('default', 'main')))

    {
      $this->view = 'default';
    }

    if (empty($this->searchString))
    {
      $this->searchString = '';
    }
  }
 /**
  * Executes navigation component
  *
  * @param string $searchString Поисковая фраза
  */
  public function executeNavigation()
  {
    $list = array();

    $list[] = array(
      'name' => "Поиск (".$this->searchString.")",
      'url'  => url_for('search', array('searchString' => $this->searchString)),
    );

    $this->setVar('list', $list, false);
  }
  /**
   * Executes filter_productType component
   *
   * @param myDoctrineCollection $productTypeList Коллекция типов товаров
   * @param string               $searchString    Строка поиска
   */
  public function executeFilter_productType()
  {
    $list = array(
      'first' => array(),
      'other' => array(),
    );

    $firstProductCategory = isset($this->productTypeList[0]->ProductCategory[0]) ? $this->productTypeList[0]->ProductCategory[0]->getRootCategory() : new ProductCategory();
    foreach ($this->productTypeList as $i => $productType)
    {
      $index = 'other';
      if ($firstProductCategory)
      {
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
        'url'      => url_for('search', array('q' => $this->searchString, 'product_type' => $productType->id)),
        'name'     => (string)$productType,
        'token'    => $productType->id,
        'count'    => isset($productType->_product_count) ? $productType->_product_count : 0,
        'value'    => $productType->id,
        'selected' => false
          || ((0 == $i) && !$this->productType)
          || (isset($productType->_selected) ? $productType->_selected : false)
        ,
      );
    }

    $variation = mb_strtolower($firstProductCategory->name, 'utf-8');
    switch ($variation)
    {
      case 'мебель':
        $variation = 'мебели';
        break;
      case 'бытовая техника':
        $variation = 'бытовой технике';
        break;
      case 'товары для дома':
        $variation = 'товарах для дома';
        break;
      case 'товары для детей':
        $variation = 'товарах для детей';
        break;
      case 'сделай сам (инструменты)':
        $variation = 'сделай сам (инструменты)';
        break;
      case 'электроника':
        $variation = 'электронике';
        break;
      case 'украшения и часы':
        $variation = 'украшениях и часах';
        break;
      case 'спорт':
        $variation = 'спорте';
        break;
      case 'подарки':
        $variation = 'подарках';
        break;
    }
    $firstProductCategory->mapValue('_variation', $variation);

    $this->setVar('list', $list, true);
    $this->setVar('firstProductCategory', $firstProductCategory, true);
  }
}
