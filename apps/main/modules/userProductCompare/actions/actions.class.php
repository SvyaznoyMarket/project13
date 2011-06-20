<?php

/**
 * userProductCompare actions.
 *
 * @package    enter
 * @subpackage userProductCompare
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userProductCompareActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->setVar('productCategoryList', $this->getUser()->getProductCompare()->getProductCategories(), true);
  }
 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();
  }
 /**
  * Executes add action
  *
  * @param sfRequest $request A request object
  */
  public function executeAdd(sfWebRequest $request)
  {
    $product = $this->getRoute()->getObject();

    if ($product)
    {
      $this->getUser()->getProductCompare()->addProduct($product);
    }

    $this->redirect($this->getRequest()->getReferer());
  }
 /**
  * Executes delete action
  *
  * @param sfRequest $request A request object
  */
  public function executeDelete(sfWebRequest $request)
  {
    $product = $this->getRoute()->getObject();

    if ($product)
    {
      $this->getUser()->getProductCompare()->deleteProduct($product->category_id, $product->id);
    }

    $this->redirect($this->getRequest()->getReferer());
  }
}
