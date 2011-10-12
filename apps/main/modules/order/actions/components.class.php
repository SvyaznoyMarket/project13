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
    if (!in_array($this->view, array('default', 'compact', 'base')))
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
    $this->setVar('total', $this->getUser()->getCart()->getTotal());
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
 /**
  * Executes step1 component
  *
  * @param OrderStep1Form $form Форма заказа 1-го шага
  */
  public function executeStep1()
  {
  }
 /**
  * Executes step2 component
  *
  * @param OrderStep2Form $form Форма заказа 2-го шага
  */
  public function executeStep2()
  {

  }
 /**
  * Executes navigation component
  *
  * @param Order $order заказ
  * @param integer $step шаг заказа
  */
  public function executeNavigation()
  {
    if (empty($this->step))
    {
      $this->step = 1;
    }

    $list = array();
    foreach (range(1, 2) as $step)
    {
      $list[] = array(
        'name'      => $step.'-й шаг',
        'url'       => url_for('order_new', array('step' => $step)),
        'is_active' => (null == $this->order->step ? 0 : $this->order->step) >= $step,
      );
    }
      $list[] = array(
        'name'      => '3-й шаг',
        'url'       => url_for('order_confirm'),
        'is_active' => 3 == $this->order->step,
      );


    $this->setVar('list', $list, true);
  }
 /**
  * Executes field_address component
  *
  * @param OrderStep1Form $form Форма заказа 1-го шага
  */
  public function executeField_address()
  {
    $this->setVar('widget', new sfWidgetFormChoice(array(
      'choices'  => $this->getUser()->isAuthenticated() ? array_merge(array('' => ''), UserAddressTable::getInstance()->getListByUser($this->getUser()->getGuardUser()->id)->toKeyValueArray('address', 'name')) : array(),
      'multiple' => false,
      'expanded' => false,
    )), true);
  }
 /**
  * Executes field_shop_id component
  *
  * @param OrderStep1Form $form Форма заказа 1-го шага
  */
  public function executeField_shop_id()
  {
  }
 /**
  * Executes field_region_id component
  *
  * @param OrderStep1Form $form Форма заказа 1-го шага
  */
  public function executeField_region_id()
  {
    $regionId = $this->form->getValue('region_id');

    $this->region = !empty($regionId) ? RegionTable::getInstance()->find($regionId) : '';

    $this->setVar('widget', new sfWidgetFormInputText(array(), array(
      'class' => 'order_region_name'
    )), true);
  }

  public function executeField_person_type()
  {
  }

  public function executeField_receipt_type()
  {
  }

  public function executeField_delivered_at()
  {
  }

  public function executeField_payment_method_id()
  {
  }

  public function executeField_recipient_last_name()
  {
  }

  public function executeField_recipient_first_name()
  {
  }

  public function executeField_delivery_type_id()
  {
  }

  public function executeField_delivery_period_id()
  {
  }

  public function executeField_recipient_phonenumbers()
  {
  }

  public function executeField_is_receive_sms()
  {
  }

  public function executeField_zip_code()
  {
  }

  public function executeField_extra()
  {
  }

  public function executeReceipt()
  {
    $this->setVar('cart', $this->getUser()->getCart());
  }

}

