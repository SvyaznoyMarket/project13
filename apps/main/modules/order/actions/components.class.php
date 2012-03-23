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

    $item = array('name' => (string)$this->order, 'token' => $this->order->token, 'number' => $this->order->number, 'sum' => (int)$this->order->sum, 'created_at' => $this->order->created_at, 'payment_method_name' => $this->order->PaymentMethod ? $this->order->PaymentMethod->name : null, 'delivered_at' => $this->order->delivered_at, 'delivery_type' => $this->order->getDeliveryType(), 'delivery_price' => $this->order->delivery_price,// 'delivered_period'    => $this->order->DeliveryPeriod ? $this->order->DeliveryPeriod->name : null,
    );

    if (in_array($this->view, array('default', 'compact')))
    {
      $item['products'] = array();
      foreach ($this->order->ProductRelation as $orderProductRelation)
      {
        $item['products'][] = array('type' => 'product', 'name' => (string)$orderProductRelation->Product, 'article' => (string)$orderProductRelation->Product->article, 'url' => $this->generateUrl('productCard', $orderProductRelation->Product), 'price' => (int)$orderProductRelation['price'], 'quantity' => $orderProductRelation['quantity'],);
      }
      $item['services'] = array();
      foreach ($this->order->ServiceRelation as $orderServiceRelation)
      {
        $item['products'][] = array('type' => 'service', 'name' => (string)$orderServiceRelation->Service, 'url' => $this->generateUrl('service_show', $orderServiceRelation->Service), 'price' => (int)$orderServiceRelation['price'], 'quantity' => $orderServiceRelation['quantity'],);
      }
    }

    $deliveryPrices = $this->getUser()->getCart()->getDeliveriesPrice();
    $this->setVar('deliveryPrice', isset($deliveryPrices[$this->order->delivery_type_id]) ? $deliveryPrices[$this->order->delivery_type_id] : null, true);
    $this->setVar('item', $item, true);
    $this->setVar('total', $this->getUser()->getCart()->getTotal());
  }

  /**
   * Executes list component
   *
   */
  public function executeList()
  {
    $list = $listProcess = $listReady = $listCancelled = array();
    foreach ($this->getUser()->getGuardUser()->getOrderList(array('with_products' => true)) as $order)
    {
      if ($order->status_id == Order::STATUS_READY) $listReady[] = $order; elseif ($order->status_id == Order::STATUS_CANCELLED) $listCancelled[] = $order; else $listProcess[] = $order;
    }
    $list = array_merge($listProcess, $listReady, $listCancelled);

    $statusList = OrderStatusTable::getInstance()->findAll()->getData();
    $this->setVar('list', $list, true);
    $this->setVar('statusList', $statusList, true);
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
    /*if (empty($this->step))
    {
      $this->step = 1;
    }

    $list = array();
    foreach (range(1, 2) as $step)
    {
      $list[] = array(
        'name'      => $step.'-й шаг',
        'url'       => $this->generateUrl('order_new', array('step' => $step)),
        'is_active' => (null == $this->order->step ? 0 : $this->order->step) >= $step,
      );
    }
      $list[] = array(
        'name'      => '3-й шаг',
        'url'       => $this->generateUrl('order_confirm'),
        'is_active' => 3 == $this->order->step,
      );


    $this->setVar('list', $list, true);*/
  }

  public function executeReceipt()
  {
    $this->setVar('cart', $this->getUser()->getCart());
  }

  function executeSeo_counters_advance()
  {
    if ($this->step == 2)
    {
      $orderArticle = $this->getUser()->getCart()->getSeoCartArticle();
      $this->setVar('orderArticle', $orderArticle, true);
    } elseif ($this->step == 4)
    {
      $qtyAr = array();
      $barcodeAr = array();
      foreach ($this->order->ProductRelation as $product)
      {
        $qtyAr[] = $product['quantity'];
        $barcodeAr[] = $product->Product['barcode'];
      }
      $qty = implode(',', $qtyAr);
      $barcodeStr = implode(',', $barcodeAr);
      $this->setVar('quantityString', $qty, true);
      echo $barcodeStr . '=============';
      $this->setVar('orderArticle', $barcodeStr, true);
    }
  }

  /**
   * Executes field_region_id component
   *
   * @param string $name Название поля
   * @param mixed $value Значение поля
   * @param array $regionList Коллекция регионов
   */
  public function executeField_region_id()
  {
    $this->region = !empty($this->value) ? RegionTable::getInstance()->getById($this->value) : false;
    $this->displayValue = $this->region ? $this->region->name : '';
  }
  /**
   * Executes field_delivery_type_id component
   *
   * @param string $name Название поля
   * @param mixed $value Значение поля
   * @param myDoctrineCollection $deliveryTypeList Коллекция типов доставки для пользователя (с учетом региона и товаров в корзине)
   */
  public function executeField_delivery_type_id()
  {
    //myDebug::dump($this->deliveryTypeList, 1);
    $choices = array();
    foreach ($this->deliveryTypeList as $deliveryType)
    {
      $choices[$deliveryType['id']] = array(
        'id'          => strtr($this->name, array('[' => '_', ']' => '_')).$deliveryType['id'],
        'label'       => $deliveryType['name'].', '.($deliveryType['price'] > 0 ? ($deliveryType['price'].' руб.') : 'бесплатно'),
        'price'       => $deliveryType['price'],
        'description' => $deliveryType['description'],
      );
    }

    $this->name = $this->name;
    $this->setVar('choices', $choices, true);
  }
  /**
   * Executes field_payment_method_id component
   *
   * @param string $name Название поля
   * @param mixed $value Значение поля
   */
  public function executeField_payment_method_id()
  {
    $choices = array();

    foreach (PaymentMethodTable::getInstance()->getList() as $paymentMethod)
    {
      if ('online' == $paymentMethod->token) continue;

      $choices[$paymentMethod->id] = array(
        'id'          => strtr($this->name, array('[' => '_', ']' => '_')).$paymentMethod->id,
        'label'       => $paymentMethod->name,
        'description' => $paymentMethod->description,
      );
    }

    $this->setVar('choices', $choices, true);
  }
}

