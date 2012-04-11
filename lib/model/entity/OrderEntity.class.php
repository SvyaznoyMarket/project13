<?php

/**
 * Заказ
 */
class OrderEntity
{
  const TYPE_ORDER = 1;
  const TYPE_PREORDER = 2;
  const TYPE_SUBORDER = 3;

  const STATUS_FORMED = 1;
  const STATUS_APPROVED_BY_CALL_CENTER = 2;
  const STATUS_FORMED_IN_STOCK = 3;
  const STATUS_IN_DELIVERY = 4;
  const STATUS_DELIVERED = 5;
  const STATUS_CANCELED = 100;

  const PAYMENT_STATUS_NOT_PAID = 1;
  const PAYMENT_STATUS_TRANSFER = 2;
  const PAYMENT_STATUS_ADVANCE = 3;
  const PAYMENT_STATUS_PAID = 4;
  const PAYMENT_STATUS_CANCELED = 5;

  /* @var integer */
  private $id;

  /* @var integer */
  private $type;

  /* @var integer */
  private $status;

  /* @var string */
  private $number;

  /* @var UserEntity */
  private $user;

  /* @var string */
  private $lastName;

  /* @var string */
  private $firstName;

  /* @var string */
  private $middleName;

  /* @var string */
  private $phonenumber;

  /* @var integer */
  private $paymentStatus;

  /* @var integer */
  private $payment;

  /* @var string */
  private $paymentDetail;

  /* @var integer */
  private $sum;

  /* @var DeliveryTypeEntity */
  private $deliveryType;

  /* @var DateTime */
  private $deliveredAt;

  /* @var array */
  private $deliveryInterval;

  /* @var ShopEntity */
  private $shop = null;

  /* @var string */
  private $address;

  /* @var Region */
  private $region;

  /* @var boolean */
  private $isSmsAlert = false;

  /* @var string */
  private $comment;

  /* @var string */
  private $ipAddress;

  /* @var DateTime */
  private $createdAt;

  /* @var DateTime */
  private $upDateTimedAt;

  /* @var OrderItem[] */
  private $item;


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
   * @param string $comment
   */
  public function setComment($comment)
  {
    $this->comment = $comment;
  }

  /**
   * @return string
   */
  public function getComment()
  {
    return $this->comment;
  }

  /**
   * @param DateTime $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }

  /**
   * @return DateTime
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * @param DateTime $deliveredAt
   */
  public function setDeliveredAt($deliveredAt)
  {
    $this->deliveredAt = $deliveredAt;
  }

  /**
   * @return DateTime
   */
  public function getDeliveredAt()
  {
    return $this->deliveredAt;
  }

  /**
   * @param array $deliveryInterval
   */
  public function setDeliveryInterval($deliveryInterval)
  {
    $this->deliveryInterval = $deliveryInterval;
  }

  /**
   * @return array
   */
  public function getDeliveryInterval()
  {
    return $this->deliveryInterval;
  }

  /**
   * @param DeliveryTypeEntity $deliveryType
   */
  public function setDeliveryType($deliveryType)
  {
    $this->deliveryType = $deliveryType;
  }

  /**
   * @return DeliveryTypeEntity
   */
  public function getDeliveryType()
  {
    return $this->deliveryType;
  }

  /**
   * @param string $firstName
   */
  public function setFirstName($firstName)
  {
    $this->firstName = $firstName;
  }

  /**
   * @return string
   */
  public function getFirstName()
  {
    return $this->firstName;
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
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }

  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }

  /**
   * @param boolean $isSmsAlert
   */
  public function setIsSmsAlert($isSmsAlert)
  {
    $this->isSmsAlert = $isSmsAlert;
  }

  /**
   * @return boolean
   */
  public function getIsSmsAlert()
  {
    return $this->isSmsAlert;
  }

  public function setItem($item)
  {
    $this->item = $item;
  }

  public function getItem()
  {
    return $this->item;
  }

  /**
   * @param string $lastName
   */
  public function setLastName($lastName)
  {
    $this->lastName = $lastName;
  }

  /**
   * @return string
   */
  public function getLastName()
  {
    return $this->lastName;
  }

  /**
   * @param string $middleName
   */
  public function setMiddleName($middleName)
  {
    $this->middleName = $middleName;
  }

  /**
   * @return string
   */
  public function getMiddleName()
  {
    return $this->middleName;
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
   * @param int $payment
   */
  public function setPayment($payment)
  {
    $this->payment = $payment;
  }

  /**
   * @return int
   */
  public function getPayment()
  {
    return $this->payment;
  }

  /**
   * @param string $paymentDetail
   */
  public function setPaymentDetail($paymentDetail)
  {
    $this->paymentDetail = $paymentDetail;
  }

  /**
   * @return string
   */
  public function getPaymentDetail()
  {
    return $this->paymentDetail;
  }

  /**
   * @param int $paymentStatus
   */
  public function setPaymentStatus($paymentStatus)
  {
    $this->paymentStatus = $paymentStatus;
  }

  /**
   * @return int
   */
  public function getPaymentStatus()
  {
    return $this->paymentStatus;
  }

  /**
   * @param string $phonenumber
   */
  public function setPhonenumber($phonenumber)
  {
    $this->phonenumber = $phonenumber;
  }

  /**
   * @return string
   */
  public function getPhonenumber()
  {
    return $this->phonenumber;
  }

  /**
   * @param Region $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }

  /**
   * @return Region
   */
  public function getRegion()
  {
    return $this->region;
  }

  /**
   * @param ShopEntity $shop
   */
  public function setShop($shop)
  {
    $this->shop = $shop;
  }

  /**
   * @return ShopEntity
   */
  public function getShop()
  {
    return $this->shop;
  }

  /**
   * @param int $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }

  /**
   * @return int
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * @param int $sum
   */
  public function setSum($sum)
  {
    $this->sum = $sum;
  }

  /**
   * @return int
   */
  public function getSum()
  {
    return $this->sum;
  }

  /**
   * @param int $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  /**
   * @return int
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param DateTime $upDateTimedAt
   */
  public function setUpDateTimedAt($upDateTimedAt)
  {
    $this->upDateTimedAt = $upDateTimedAt;
  }

  /**
   * @return DateTime
   */
  public function getUpDateTimedAt()
  {
    return $this->upDateTimedAt;
  }

  /**
   * @param UserEntity $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }

  /**
   * @return UserEntity
   */
  public function getUser()
  {
    return $this->user;
  }
}