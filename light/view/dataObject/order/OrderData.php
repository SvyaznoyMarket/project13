<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 31.07.12
 * Time: 13:17
 * To change this template use File | Settings | File Templates.
 */

require_once(Config::get('viewPath').'dataObject/UserData.php');
require_once('OrderProductData.php');
require_once('OrderServiceData.php');
require_once('OrderDeliveryData.php');

class OrderData
{
  const TYPE_ORDER = 1;
  const TYPE_PREORDER = 2;
  const TYPE_CUSTOM = 3;
  const TYPE_1CLICK = 9;

  const STATUS_FORMED = 1; //Новый заказ
  const STATUS_APPROVED_BY_CALL_CENTER = 2; //Подтвержден
  const STATUS_FORMED_IN_STOCK = 3; //Собран на складе
  const STATUS_IN_DELIVERY = 4; //Доставляется
  const STATUS_DELIVERED = 5; //Выполнен
  const STATUS_CANCELED = 100; //Отменен

  const PAYMENT_STATUS_NOT_PAID = 1;
  const PAYMENT_STATUS_TRANSFER = 2;
  const PAYMENT_STATUS_ADVANCE = 3;
  const PAYMENT_STATUS_PAID = 4;
  const PAYMENT_STATUS_CANCELED = 5;

  /** @var int */
  private $id;

  /** @var string */
  private $number;

  /** @var int */
  private $typeId;

  /** @var int */
  private $paymentId;

  /** @var int */
  private $paymentStatusId;

  /** @var int */
  private $geoId;

  /** @var OrderDeliveryData */
  private $deliveryInfo;

  /** @var UserData */
  private $user;

  /** @var string */
  private $extra;

  /** @var string */
  private $ip;

  /** @var bool */
  private $isReceiveSms = false;

  /** @var string */
  private $svyaznoyClubCardNumber;

  /** @var OrderProductData[] */
  private $productList = array();

  /** @var OrderServiceData[] */
  private $serviceList = array();

  /** @var float */
  private $totalPrice;

  public function __construct($data = array()){
    $this->user = new UserData();

    if(!is_array($data)){
      $this->deliveryInfo = new OrderDeliveryData(array());
      return;
    }

    if(array_key_exists('user_id', $data))           $this->user->setId((int)$data['user_id']);
    if(array_key_exists('last_name', $data))         $this->user->setLastName((string)$data['last_name']);
    if(array_key_exists('first_name', $data))        $this->user->setFirstName((string)$data['first_name']);
    if(array_key_exists('middle_name', $data))       $this->user->setMiddleName((string)$data['middle_name']);
    if(array_key_exists('email', $data))             $this->user->setEmail((string)$data['email']);
    if(array_key_exists('mobile', $data))            $this->user->setPhone((string)$data['mobile']);

    if(array_key_exists('id', $data))                    $this->id                  = (int)$data['id'];
    if(array_key_exists('number', $data))                $this->number              = (string)$data['number'];
    if(array_key_exists('type_id', $data))               $this->typeId              = (int)$data['type_id'];
    if(array_key_exists('payment_id', $data))            $this->paymentId           = (int)$data['payment_id'];
    if(array_key_exists('payment_status_id', $data))     $this->paymentStatusId     = (int)$data['payment_status_id'];
    if(array_key_exists('geo_id', $data))                $this->geoId               = (int)$data['geo_id'];
    if(array_key_exists('extra', $data))                 $this->extra               = (string)$data['extra'];
    if(array_key_exists('ip', $data))                    $this->ip                  = (string)$data['ip'];
    if(array_key_exists('is_receive_sms', $data))        $this->isReceiveSms        = (bool)$data['is_receive_sms'];
    if(array_key_exists('svyaznoy_club_card_number', $data)) $this->svyaznoyClubCardNumber = (string)$data['svyaznoy_club_card_number'];

    if(array_key_exists('sum', $data))                   $this->totalPrice          = $data['sum'];
    if(array_key_exists('total_price', $data))           $this->totalPrice          = $data['total_price'];

    $this->deliveryInfo = new OrderDeliveryData($data);

    if(array_key_exists('product', $data) && is_array($data['product'])){
      $this->productList = array();
      foreach($data['product'] as $product){
        $this->productList[] = new OrderProductData($product);
      }
    }

    if(array_key_exists('service', $data) && is_array($data['service'])){
      $this->serviceList = array();
      foreach($data['service'] as $service){
        $this->serviceList[] = new OrderServiceData($service);
      }
    }
  }

  /**
   * @param string $extra
   */
  public function setExtra($extra)
  {
    $this->extra = $extra;
  }

  /**
   * @return string
   */
  public function getExtra()
  {
    return $this->extra;
  }

  /**
   * @param int $geoId
   */
  public function setGeoId($geoId)
  {
    $this->geoId = $geoId;
  }

  /**
   * @return int
   */
  public function getGeoId()
  {
    return $this->geoId;
  }

  /**
   * @param string $ip
   */
  public function setIp($ip)
  {
    $this->ip = $ip;
  }

  /**
   * @return string
   */
  public function getIp()
  {
    return $this->ip;
  }

  /**
   * @param boolean $isReceiveSms
   */
  public function setIsReceiveSms($isReceiveSms)
  {
    $this->isReceiveSms = $isReceiveSms;
  }

  /**
   * @return boolean
   */
  public function isReceiveSms()
  {
    return $this->isReceiveSms;
  }

  /**
   * @param int $paymentId
   */
  public function setPaymentId($paymentId)
  {
    $this->paymentId = $paymentId;
  }

  /**
   * @return int
   */
  public function getPaymentId()
  {
    return $this->paymentId;
  }

  /**
   * @param int $paymentStatusId
   */
  public function setPaymentStatusId($paymentStatusId)
  {
    $this->paymentStatusId = $paymentStatusId;
  }

  /**
   * @return int
   */
  public function getPaymentStatusId()
  {
    return $this->paymentStatusId;
  }

  /**
   * @param OrderProductData[] $productList
   */
  public function setProductList($productList)
  {
    $this->productList = $productList;
  }

  /**
   * @return array|OrderProductData[]
   */
  public function getProductList()
  {
    return $this->productList;
  }

  /**
   * @param OrderServiceData[] $serviceList
   */
  public function setServiceList($serviceList)
  {
    $this->serviceList = $serviceList;
  }

  /**
   * @return OrderServiceData[]
   */
  public function getServiceList()
  {
    return $this->serviceList;
  }

  /**
   * @param string $svyaznoyClubCardNumber
   */
  public function setSvyaznoyClubCardNumber($svyaznoyClubCardNumber)
  {
    $this->svyaznoyClubCardNumber = $svyaznoyClubCardNumber;
  }

  /**
   * @return string
   */
  public function getSvyaznoyClubCardNumber()
  {
    return $this->svyaznoyClubCardNumber;
  }

  /**
   * @param int $typeId
   */
  public function setTypeId($typeId)
  {
    $this->typeId = $typeId;
  }

  /**
   * @return int
   */
  public function getTypeId()
  {
    return $this->typeId;
  }

  /**
   * @param \light\UserData $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }

  /**
   * @return \light\UserData
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * @param \light\OrderDeliveryData $deliveryInfo
   */
  public function setDeliveryInfo($deliveryInfo)
  {
    $this->deliveryInfo = $deliveryInfo;
  }

  /**
   * @return \light\OrderDeliveryData
   */
  public function getDeliveryInfo()
  {
    return $this->deliveryInfo;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param string $number
   */
  public function setNumber($number)
  {
    $this->number = $number;
  }

  /**
   * @return string
   */
  public function getNumber()
  {
    return $this->number;
  }

  /**
   * @param float $totalPrice
   */
  public function setTotalPrice($totalPrice)
  {
    $this->totalPrice = $totalPrice;
  }

  /**
   * @return float
   */
  public function getTotalPrice()
  {
    return $this->totalPrice;
  }
}
