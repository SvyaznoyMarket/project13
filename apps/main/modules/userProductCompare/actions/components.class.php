<?php

/**
 * userProductCompare components.
 *
 * @package    enter
 * @subpackage userProductCompare
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userProductCompareComponents extends myComponents
{
 /**
  * Executes button component
  *
  * @param Product $product Товар
  */
  public function executeButton()
  {
    $productCompare = $this->getUser()->getProductCompare();

    if (isset($this->product))
    {
      $this->button = $productCompare->hasProduct($this->product['type_id'], $this->product['id']) ? 'show' : 'add';
      $this->productType = $this->product['Type'];
    }
    else
    {
      if (!$productCompare->hasProductType($this->productType['id']))
      {
        return sfView::NONE;
      }

      $this->button = 'show';
    }

    if (!in_array($this->view, array()))
    {
      $this->view = 'default';
    }
  }
 /**
  * Executes show component
  *
  * @param ProductCategory $productCategory Категория товара
  */
  public function executeShow()
  {
    $productCompare = $this->getUser()->getProductCompare();
    if (!$productCompare->hasProductType($this->productType->id))
    {
      return sfView::NONE;
    }

    $this->productList = $productCompare->getProducts($this->productType->id);

    $list = array();
    if (count($this->productList) > 0)
    {
      // FIXME: использовать связь категории и типов товаров
      $productType = ProductTypeTable::getInstance()->getById($this->productList[0]->Type->id, array('view' => 'show'));

      foreach ($productType->PropertyGroup as $productPropertyGroup)
      {
        $list[] = array(
          'type' => 'group',
          'name' => (string)$productPropertyGroup,
        );

        foreach ($productPropertyGroup->Property as $productProperty)
        {
          $values = array();
          foreach ($this->productList as $product)
          {
            if ($productParameter = $product->getParameterByProperty($productProperty->id))
            {
              $values[] = $productParameter->getValue();
            }
          }

          $list[] = array(
            'type'   => 'property',
            'name'   => (string)$productProperty,
            'values' => $values,
          );
        }
      }
    }

    $this->setVar('productCount', count($this->productList), true);
    $this->setVar('list', $list, true);
    $this->setVar('productList', $this->productList, true);
  }
}
