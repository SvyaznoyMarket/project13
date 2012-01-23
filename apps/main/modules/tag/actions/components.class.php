<?php

/**
 * tag components.
 *
 * @package    enter
 * @subpackage tag
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tagComponents extends myComponents
{
 /**
  * Executes list component
  *
  * @param myDoctrineCollection $tagList Список тегов
  */
  public function executeList()
  {
    $list = array();

    foreach ($this->tagList as $tag)
    {
      $list[] = array(
        'name'  => (string)$tag,
        'token' => $tag->token,
        'url'   => $this->generateUrl('tag_show', array('tag' => $tag->token)),
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes navigation component
  *
  * @param Tag $tag Тег
  */
  public function executeNavigation()
  {
    $list = array();

    /*
    $list[] = array(
      'name' => 'Теги',
      'url'  => $this->generateUrl('tag'),
    );
    */

    if ($this->tag)
    {
      $list[] = array(
        'name' => (string)$this->tag,
        'url'  => $this->generateUrl('tag_show', array('tag' => $this->tag->token)),
      );
    }

    $this->setVar('list', $list, false);
  }
  /**
   * Executes filter_productType component
   *
   * @param myDoctrineCollection $productTypeList Коллекция типов товаров
   * @param ProductType          $productType     Выбранный тип товара
   * @param Tag                  $tag             Тэг
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
        'url'      => $this->generateUrl('tag_show', array('tag' => $this->tag->token, 'productType' => $productType->token)),
        'name'     => (string)$productType,
        'token'    => $productType->id,
        'count'    => isset($productType->_product_count) ? $productType->_product_count : 0,
        'value'    => $productType->id,
        'selected' => false
          || ((0 == $i) && !$this->productType)
          || ($this->productType && ($this->productType->id == $productType->id))
        ,
      );
    }

    $this->setVar('list', $list, true);
    $this->setVar('firstProductCategory', $firstProductCategory, true);
  }
}
