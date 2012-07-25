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
    if (!in_array($this->view, array('default', 'compact', 'base'))) {
      $this->view = 'default';
    }

    $item = array(
      'name' => (string)$this->order,
      'token' => $this->order->token,
      'number' => $this->order->number,
      'sum' => (int)$this->order->sum,
      'created_at' => $this->order->created_at,
      'payment_method_name' => $this->order->getPaymentMethod() ? $this->order->getPaymentMethod()->getName() : null,
      'delivered_at' => $this->order->delivered_at,
      'delivery_type' => $this->order->getDeliveryType(),
      'delivery_price' => $this->order->delivery_price,
      // 'delivered_period'    => $this->order->DeliveryPeriod ? $this->order->DeliveryPeriod->name : null,
    );

    if (in_array($this->view, array('default', 'compact'))) {
      $item['products'] = array();
      $item['services'] = array();
      foreach ($this->order->getItem() as $orderItem)
      {
        /* @var $orderItem OrderItemEntity */
        if (OrderItemEntity::TYPE_PRODUCT == $orderItem->getType()) {
          $item['products'][] = array(
            'type'     => 'product',
            'name'     => $orderItem->getProduct()->getName(),
            'article'  => $orderItem->getProduct()->getArticle(),
            'url'      => $orderItem->getProduct()->getLink(),
            'price'    => $orderItem->getPrice(),
            'quantity' => $orderItem->getQuantity(),
          );
        }
        else if (OrderItemEntity::TYPE_SERVICE == $orderItem->getType()) {
          $item['products'][] = array(
            'type'     => 'service',
            'name'     => (string)$orderItem->getService()->getName(),
            'url'      => $this->generateUrl('service_show', array('service' => $orderItem->getService()->getToken())),
            'price'    => $orderItem->getPrice(),
            'quantity' => $orderItem->getQuantity(),
          );
        }
      }
    }

    $deliveryPrices = $this->getUser()->getCart()->getDeliveriesPrice();
    $this->setVar('deliveryPrice', isset($deliveryPrices[$this->order->getDeliveryType()->getId()]) ? $deliveryPrices[$this->order->getDeliveryType()->getId()] : null, true);
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
    foreach (RepositoryManager::getOrder()->getByUserToken($this->getUser()->getGuardUser()->getToken()) as $order)
    {
      if ($order->status == Order::STATUS_READY) $listReady[] = $order;
      elseif ($order->status == Order::STATUS_CANCELLED) $listCancelled[] = $order;
      else $listProcess[] = $order;
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

  /**
   * Executes field_address component
   *
   * @param OrderStep1Form $form Форма заказа 1-го шага
   */
  public function executeField_address()
  {
    $this->setVar('widget', new sfWidgetFormChoice(array(
      'choices' => $this->getUser()->isAuthenticated() ? array_merge(array('' => ''), UserAddressTable::getInstance()->getListByUser($this->getUser()->getGuardUser()->id)->toKeyValueArray('address', 'name')) : array(),
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

  public function executeField_agreed()
  {
  }

	public function executeField_sclub_card_number()
	{
//		echo 'fied sclub_card_number execute !!';
//		exit();
	}

  public function executeReceipt()
  {
    $cart = $this->getUser()->getCart();

    $prods = $cart->getProducts();
    $services = $cart->getServices();

    $prodIdList = array_keys($prods);
    $serviceIdList = array_keys($services);

    $list = array();

    $prodCb = function($data) use($list, $prods){
      /** @var $data ProductEntity[] */

      foreach($data as $product){
        /** @var $cartInfo \light\ProductCartData */
        $cartInfo = $prods[$product->getId()];

        $list[] = array(
          'type' => 'products',
          'name' => $product->getName(),
          'token' => $product->getToken(),
          'token_prefix' => $product->getPrefix(),
          'quantity' => $cartInfo->getQuantity(),
          'price' => number_format($cartInfo->getPrice(), 0, ',', ' '),
        );
      }
    };

    $serviceCb = function($data) use($list, $services){
      /** @var $data ServiceEntity[] */

      foreach($data as $serviceCoreInfo){
        /** @var $cartInfo \light\ServiceCartData[]  array('productId' => ServiceCartData, 'productId' => ServiceCartData)*/
        $cartInfo = $services[$serviceCoreInfo->getId()];
        $qty = 0;
        $price = 0;

        foreach ($cartInfo as $prodId => $prodServInfo)
        {
          $qty += $prodServInfo->getQuantity();
          $price += $prodServInfo->getTotalPrice();
        }
        $list[] = array(
          'type' => 'service',
          'name' => $serviceCoreInfo->getName(),
          'token' => $serviceCoreInfo->getToken(),
          'quantity' => $qty,
          'price' => number_format($price, 0, ',', ' '),
        );
      }
    };
    RepositoryManager::getProduct()->getListByIdAsync($prodCb, $prodIdList, true);
    RepositoryManager::getService()->getListByIdAsync($serviceCb, $serviceIdList, true);
    CoreClient::getInstance()->execute();


    $this->setVar('cart', $list);
    $this->setVar('total', number_format($cart->getTotal(), 0, ',', ' '));
  }

  function executeSeo_counters_advance()
  {
    if ($this->step == 2) {
      $orderArticle = $this->getUser()->getCart()->getSeoCartArticle();
      $this->setVar('orderArticle', $orderArticle, true);
    } elseif ($this->step == 4) {
      $qtyAr = array();
      $barcodeAr = array();
      foreach ($this->order->ProductRelation as $product) {
        $qtyAr[] = $product['quantity'];
        $barcodeAr[] = $product->Product['core_id'];
      }
      $qty = implode(',', $qtyAr);
      $barcodeStr = implode(',', $barcodeAr);
      $this->setVar('quantityString', $qty, true);
      $this->setVar('orderArticle', $barcodeStr, true);
    }
  }

  function executeSeo_admitad() {

      //идентификатор категории для admited - core_id
      // myDebug::dump($this->order);

      $catIdList = array();
      $data = array();
      foreach ($this->order->ProductRelation as $product) {
         $catIdList[] = $product->Product->Category[0]['root_id'];
         $data[$product->Product->Category[0]['root_id']]['sum'] += $product['price'] * $product['quantity'];
         $data[$product->Product->Category[0]['root_id']]['number'] = $this->order->number;
      }
      if (!$catIdList) {
          return;
      }
      $catList = ProductCategoryTable::getInstance()->createBaseQuery()->whereIn('id', $catIdList)->fetchArray();
      $resultData = array();

      foreach ($catList as $cat) {
          $resultData[$cat['core_id']] = $data[$cat['id']];
          $resultData[$cat['core_id']]['number'] .= '-' . $cat['core_id'];
      }
      $uid = $this->getRequest()->getCookie(sfConfig::get('app_admitad_cookie_name', 'admitad_uid'));
      if(!$uid || strlen($uid) != 32){
        $uid = false;
      }

      if ($uid) {
        $resultData['uid'] = $uid;
      } else {
        $resultData['uid'] = '';
      }

      //print_r($resultData);
      $this->setVar('data', $resultData, true);
  }

}

