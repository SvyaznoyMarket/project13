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

    if ($productCompare->hasProduct($this->product->category_id, $this->product->id))
    {
      $this->button = 'show';
    }
    else
    {
      $this->button = 'add';
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

    $list = array();
    foreach ($productCompare->getProducts($this->productCategory->id) as $product)
    {
      $list[] = array(
        'name'    => (string)$product,
        'product' => $product,
      );
    }

    $this->setVar('list', $list, true);
  }
}
