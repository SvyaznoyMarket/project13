<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 08.08.12
 * Time: 11:47
 * To change this template use File | Settings | File Templates.
 */
class OrderDeliveryData
{

  /** @var int */
  private $shopId;

  /** @var int */
  private $deliveryTypeId;

  /** @var string format: "2012-05-20" */
  private $deliveryDate;

  /** @var int */
  private $deliveryIntervalId;

  /** @var int */
  private $addressId;

  /** @var string */
  private $address;

  /** @var string */
  private $zipCode;

  public function __construct($data = array()){
    if(array_key_exists('shop_id', $data))               $this->shopId              = (int)$data['shop_id'];
    if(array_key_exists('delivery_type_id', $data))      $this->deliveryTypeId      = (int)$data['delivery_type_id'];
    if(array_key_exists('delivery_date', $data))         $this->deliveryDate        = (string)$data['delivery_date'];
    if(array_key_exists('delivery_interval_id', $data))  $this->deliveryIntervalId  = (int)$data['delivery_interval_id'];
    if(array_key_exists('address_id', $data))            $this->addressId           = (int)$data['address_id'];
    if(array_key_exists('address', $data))               $this->address             = (string)$data['address'];
    if(array_key_exists('zip_code', $data))              $this->zipCode             = (string)$data['zip_code'];
  }

  /**
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }

  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }

  /**
   * @param int $addressId
   */
  public function setAddressId($addressId)
  {
    $this->addressId = $addressId;
  }

  /**
   * @return int
   */
  public function getAddressId()
  {
    return $this->addressId;
  }

  /**
   * @param string $deliveryDate
   */
  public function setDeliveryDate($deliveryDate)
  {
    $this->deliveryDate = $deliveryDate;
  }

  /**
   * @return string
   */
  public function getDeliveryDate()
  {
    return $this->deliveryDate;
  }

  /**
   * @param int $deliveryIntervalId
   */
  public function setDeliveryIntervalId($deliveryIntervalId)
  {
    $this->deliveryIntervalId = $deliveryIntervalId;
  }

  /**
   * @return int
   */
  public function getDeliveryIntervalId()
  {
    return $this->deliveryIntervalId;
  }

  /**
   * @param int $deliveryTypeId
   */
  public function setDeliveryTypeId($deliveryTypeId)
  {
    $this->deliveryTypeId = $deliveryTypeId;
  }

  /**
   * @return int
   */
  public function getDeliveryTypeId()
  {
    return $this->deliveryTypeId;
  }

  /**
   * @param int $shopId
   */
  public function setShopId($shopId)
  {
    $this->shopId = $shopId;
  }

  /**
   * @return int
   */
  public function getShopId()
  {
    return $this->shopId;
  }

  /**
   * @param string $zipCode
   */
  public function setZipCode($zipCode)
  {
    $this->zipCode = $zipCode;
  }

  /**
   * @return string
   */
  public function getZipCode()
  {
    return $this->zipCode;
  }

}
