<?php

/**
 * service components.
 *
 * @package    enter
 * @subpackage order
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class serviceComponents extends myComponents
{
 /**
  * Executes listByProduct component
  *
  * @param product $product Продукт
  */
  public function executeListByProduct()
  {
    $list = $this->product->getServiceList();

    $this->setVar('list', $list, true);
  }

  /**
  * Executes show component
  *
  * @param Service $service Услуга
  */
  public function executeShow()
  {
    $this->setVar('item', array(
      'name'         => (string)$this->service,
      'description'  => $this->service->description,
      'price'        => $this->service->Price->getFirst()->price,
    ), true);
  }
}

