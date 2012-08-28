<?php

/**
 * Заказ
 */
class OrderEntity implements ArrayAccess
{
  const TYPE_ORDER = 1;
  const TYPE_PREORDER = 2;
  const TYPE_CUSTOM = 3;
  const TYPE_1CLICK = 9;

  const STATUS_FORMED = 1;
  const STATUS_READY = 6;
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

  /* @var integer */
  private $deliveryPrice;

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
  private $updateAt;

  /* @var OrderItem[] */
  private $item;


  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))                $this->id               = (int)$data['id'];
    if (array_key_exists('type_id', $data))           $this->type             = (int)$data['type_id'];
    if (array_key_exists('status_id', $data))         $this->status           = (int)$data['status_id'];
    if (array_key_exists('number', $data))            $this->number           = (string)$data['number'];
    if (array_key_exists('user', $data))              $this->User             = new UserEntity($data['user']);
    if (array_key_exists('last_name', $data))         $this->lastName         = (string)$data['last_name'];
    if (array_key_exists('first_name', $data))        $this->firstName        = (string)$data['first_name'];
    if (array_key_exists('middle_name', $data))       $this->middleName       = (string)$data['middle_name'];
    if (array_key_exists('mobile', $data))            $this->phonenumber      = (string)$data['mobile'];
    if (array_key_exists('payment_status_id', $data)) $this->paymentStatus    = (int)$data['payment_status_id'];
    if (array_key_exists('payment_id', $data))        $this->payment          = (int)$data['payment_id'];
    if (array_key_exists('payment_detail', $data))    $this->paymentDetail    = (string)$data['payment_detail'];
    if (array_key_exists('sum', $data))               $this->sum              = (string)$data['sum'];
    if (array_key_exists('delivery_type_id', $data))  $this->deliveryType     = new DeliveryTypeEntity(array('id' => $data['delivery_type_id']));
    if (array_key_exists('delivery_type', $data))     $this->deliveryType     = new DeliveryTypeEntity($data['delivery_type']);
    if (array_key_exists('delivery_date', $data))     $this->deliveredAt      = (string)$data['delivery_date'];
    if (array_key_exists('interval', $data))          $this->deliveryInterval = $data['interval'];
    if (array_key_exists('shop_id', $data))           $this->shop             = new ShopEntity(array('id' => $data['shop_id']));
    if (array_key_exists('address', $data))           $this->address          = (string)$data['address'];
    if (array_key_exists('geo', $data))               $this->region           = new RegionEntity($data['geo']);
    if (array_key_exists('is_receive_sms', $data))    $this->isSmsAlert       = 1 == $data['is_receive_sms'];
    if (array_key_exists('extra', $data))             $this->comment          = (string)$data['extra'];
    if (array_key_exists('ip', $data))                $this->ipAddress        = (string)$data['ip'];
    if (array_key_exists('added', $data))             $this->createdAt        = (string)$data['added'];
    if (array_key_exists('updated', $data))           $this->updateAt         = (string)$data['updated'];
    if (array_key_exists('delivery', $data) && count($data['delivery'])) $this->deliveryPrice = (int)$data['delivery'][0]['price'];

    if (array_key_exists('product', $data)) foreach ($data['product'] as $orderItemData) {
      $this->item[] = new OrderItemEntity(array_merge($orderItemData, array('type' => OrderItemEntity::TYPE_PRODUCT)));
    }

    if (array_key_exists('service', $data)) foreach ($data['service'] as $orderItemData) {
      $this->item[] = new OrderItemEntity(array_merge($orderItemData, array('type' => OrderItemEntity::TYPE_SERVICE)));
    }
  }

  public function __get($key){

    return call_user_func(array($this, 'get'.sfInflector::camelize($key)));
  }

  public function __set($key, $value){

    call_user_func_array(array($this, 'set'.sfInflector::camelize($key)), array($value));
  }

  public function __toString()
  {
    return (string)$this->number;
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

  /**
   * @param \DateTime $updateAt
   */
  public function setUpdateAt($updateAt)
  {
    $this->updateAt = $updateAt;
  }

  /**
   * @return \DateTime
   */
  public function getUpdateAt()
  {
    return $this->updateAt;
  }

  public function getToken()
  {
    return $this->number;
  }

  public function getPaymentMethod()
  {
    $return = null;

    if ($this->payment) {
      PaymentMethodTable::getInstance()->find($this->payment);
    }

    return $return;
  }

  /**
   * @param int $deliveryPrice
   */
  public function setDeliveryPrice($deliveryPrice)
  {
    $this->deliveryPrice = $deliveryPrice;
  }

  /**
   * @return int
   */
  public function getDeliveryPrice()
  {
    return $this->deliveryPrice;
  }

  /**
   * Returns true if the request parameter exists (implements the ArrayAccess interface).
   *
   * @param  string $name The name of the request parameter
   *
   * @return Boolean true if the request parameter exists, false otherwise
   */
  public function offsetExists($name)
  {
    return method_exists($this, 'get'.sfInflector::camelize($name));
  }

  /**
   * Returns the request parameter associated with the name (implements the ArrayAccess interface).
   *
   * @param  string $name  The offset of the value to get
   *
   * @return mixed The request parameter if exists, null otherwise
   */
  public function offsetGet($name)
  {
    return call_user_func(array($this, 'get'.sfInflector::camelize($name)));
  }

  /**
   * Sets the request parameter associated with the offset (implements the ArrayAccess interface).
   *
   * @param string $offset The parameter name
   * @param string $value The parameter value
   */
  public function offsetSet($offset, $value)
  {
    call_user_func(array($this, 'set'.sfInflector::camelize($offset)), array($value));
  }

  /**
   * Removes a request parameter.
   *
   * @param string $offset The parameter name
   */
  public function offsetUnset($offset)
  {
    call_user_func(array($this, 'set'.sfInflector::camelize($offset)), array(null));
  }
}