<?php

/**
 * Заказ
 */
class OrderEntity
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
  private $typeId;

  /**
   * @var integer
   */
  private $statusId;

  /* @var string */
  private $number;

  /* @var string */
  private $lastName;

  /* @var string */
  private $firstName;

  /* @var string */
  private $middleName;

  /* @var string */
  private $phoneNumber;

  /* @var integer */
  private $paymentStatusId;

  /* @var integer */
  private $paymentId;

  /* @var string */
  private $paymentDetail;

  /* @var integer */
  private $sum;

  /* @var DeliveryTypeEntity */
  private $deliveryType;

  /* @var array */
  private $deliveryInterval;

  /** @var float */
  private $deliveryPrice;

  /** @var string */
  private $deliveryDate;

  /* @var ShopEntity */
  private $shop = null;

  /* @var string */
  private $address;

  /* @var Region */
  private $region;

  /* @var boolean */
  private $isSmsAlert = false;

  /* @var string */
  private $ipAddress;

  /* @var string */
  private $createdAt;

  /* @var string */
  private $updatedAt;

  /* @var OrderItemEntity[] */
  private $item;

  /** @var bool */
  private $isBill;


    public function __construct(array $data = array()){
      if(array_key_exists('id', $data))      $this->id       = (int)$data['id'];
      if(array_key_exists('type_id', $data))      $this->typeId       = (int)$data['type_id'];
      if(array_key_exists('status_id', $data))   $this->statusId      = (int)$data['status_id'];
      if(array_key_exists('number', $data))   $this->number    = $data['number'];
      if(array_key_exists('last_name', $data))   $this->lastName    = $data['last_name'];
      if(array_key_exists('first_name', $data))   $this->firstName    = $data['first_name'];
      if(array_key_exists('middle_name', $data))   $this->middleName    = $data['middle_name'];
      if(array_key_exists('mobile', $data)){
        $this->phoneNumber    = $data['mobile'];
      }
      elseif(array_key_exists('phone', $data)){
        $this->phoneNumber    = $data['phone'];
      }
      if(array_key_exists('payment_status_id', $data))   $this->paymentStatusId    = $data['payment_status_id'];
      if(array_key_exists('payment_id', $data))   $this->paymentId    = $data['payment_id'];
      if(array_key_exists('payment_detail', $data))   $this->paymentDetail    = $data['payment_detail'];
      if(array_key_exists('sum', $data))   $this->sum    = $data['sum'];
     // if(array_key_exists('is_delivery', $data))   $this->is    = $data['is_delivery'];
    //  if(array_key_exists('is_paid_delivery', $data))   $this->i    = $data['is_paid_delivery'];
      //if(array_key_exists('store_id', $data))   $this->st    = $data['store_id'];
      if(array_key_exists('shop_id', $data))   $this->shop    = $data['shop_id'];
      //if(array_key_exists('address_id', $data))   $this->addressId    = $data['address_id'];
      if(array_key_exists('geo_id', $data))   $this->region    = $data['geo_id'];
      if(array_key_exists('address', $data))   $this->address    = $data['address'];
      //if(array_key_exists('zip_code', $data))   $this->z    = $data['zip_code'];
      if(array_key_exists('ip', $data))   $this->ipAddress    = $data['ip'];
      if(array_key_exists('added', $data))   $this->createdAt    = $data['added'];
      if(array_key_exists('updated', $data))   $this->updatedAt    = $data['updated'];

      if(array_key_exists('credit_bank', $data)) {
          foreach ($data['credit_bank'] as $bankData) {
              $this->credit_bank[] = new CreditBankEntity($bankData);
          }
      }
      if(array_key_exists('product', $data)) {
        foreach ($data['product'] as $productData) {
          $itemEntity = new OrderItemEntity($productData);
          $itemEntity->setCreatedAt($this->createdAt);
          $itemEntity->setOrderId($this->getId());
          if(array_key_exists('product_id', $productData)){
            //V1
            $itemEntity->setProductId((int) $productData['product_id']);
          }
          else{
            //V2
            $itemEntity->setProductId((int) $productData['id']);
          }

          $this->addItem($itemEntity);
        }
      }
      if(array_key_exists('service', $data)) {
        foreach ($data['service'] as $productData) {
          $itemEntity = new OrderItemEntity($productData);
          $itemEntity->setCreatedAt($this->createdAt);
          $itemEntity->setOrderId($this->getId());
          $itemEntity->setProductId((int) $productData['product_id']);
          if(array_key_exists('service_id', $productData)){
            //V1
            $itemEntity->setServiceId((int) $productData['service_id']);
          }
          else{
            //V2
            $itemEntity->setServiceId((int) $productData['id']);
          }
          $this->addItem($itemEntity);
        }
      }

      if(array_key_exists('delivery_type_id', $data))       $this->deliveryType     = $data['delivery_type_id'];
      if(array_key_exists('delivery_interval_id', $data))   $this->deliveryInterval = $data['delivery_interval_id'];
      if(array_key_exists('delivery_date', $data))          $this->deliveryDate     = $data['delivery_date'];
      if(array_key_exists('is_bill', $data))                $this->isBill           = $data['is_bill'];


      if(array_key_exists('delivery', $data) && array_key_exists('price', $data['delivery'])) {
        $this->deliveryPrice = $data['delivery']['price'];
      }
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
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }

  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
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

  /**
   * @param $item OrderItemEntity
   */
  public function addItem($item)
  {
    $this->item[] = $item;
  }

  /**
   * @return OrderItemEntity[]
   */
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
  public function setPaymentId($payment)
  {
    $this->paymentId = $payment;
  }

  /**
   * @return int
   */
  public function getPaymentId()
  {
    return $this->paymentId;
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
  public function setPaymentStatusId($paymentStatus)
  {
    $this->paymentStatusId = $paymentStatus;
  }

  /**
   * @return int
   */
  public function getPaymentStatusId()
  {
    return $this->paymentStatusId;
  }

  /**
   * @param string $phonenumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }

  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
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
   * @param \DateTime $updateAt
   */
  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;
  }

  /**
   * @return \DateTime
   */
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  /**
   * @param int $statusId
   */
  public function setStatusId($statusId)
  {
    $this->statusId = $statusId;
  }

  /**
   * @return int
   */
  public function getStatusId()
  {
    return $this->statusId;
  }

  /**
   * @param float $deliveryPrice
   */
  public function setDeliveryPrice($deliveryPrice)
  {
    $this->deliveryPrice = $deliveryPrice;
  }

  /**
   * @return float
   */
  public function getDeliveryPrice()
  {
    return $this->deliveryPrice;
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
     * @param boolean $isBill
     */
    public function setIsBill($isBill) {
        $this->isBill = $isBill;
    }

    /**
     * @return boolean
     */
    public function getIsBill() {
        return $this->isBill;
    }
}