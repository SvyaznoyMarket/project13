<?php
/**
 * similarProduct components.
 *
 * @package    enter
 * @subpackage similarProduct
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class similarProductComponents extends myComponents
{
 /**
  * Executes list component
  *
  * @param Product $product Товар для которого нужно получить список аналогичных
  */
  public function executeList()
  {
    $productSimilarList = $this->product->getSimilarProduct();
    //myDebug::dump($productSimilarList);
    $this->setVar('productList', $productSimilarList, true);
  }
}