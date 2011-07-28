<?php

/**
 * order components.
 *
 * @package    enter
 * @subpackage order
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class orderComponents extends myComponents
{
 /**
  * Executes show component
  *
  * @param order $order Заказ
  */
  public function executeShow()
  {
    if (!in_array($this->view, array('default', 'compact')))
    {
      $this->view = 'default';
    }

    $item = array(
      'name'  => (string)$this->order,
      'token' => $this->order->token,
      'sum'   => $this->order->sum,
    );

    if ('default' == $this->view)
    {
      $item['products'] = array();
      foreach ($this->order->ProductRelation as $orderProductRelation)
      {
        $item['products'][] = array(
          'name'     => (string)$orderProductRelation->Product,
          'url'      => url_for('productCard', $orderProductRelation->Product),
          'price'    => $orderProductRelation['formatted_price'],
          'quantity' => $orderProductRelation['quantity'],
        );
      }
    }

    $this->setVar('item', $item, true);
  }
 /**
  * Executes list component
  *
  */
  public function executeList()
  {
    $list = array();
    foreach ($this->getUser()->getGuardUser()->getOrderList() as $order)
    {
      $list[] = array(
        'order' => $order,
        'name'  => (string)$order,
        'token' => $order->token,
        'sum'   => $order->sum,
        'url'   => url_for('order_show', $order),
      );
    }

    $this->setVar('list', $list, true);
  }
}

