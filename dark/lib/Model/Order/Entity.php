<?php

namespace Model\Order;

class Entity {
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

    /** @var int */
    private $id;
    /** @var int */
    private $typeId;
    /** @var int */
    private $statusId;
    /** @var string */
    private $number;
    /** @var int */
    private $userId;
    /** @var bool */
    private $isLegal;
    /** @var string */
    private $lastName;
    /** @var string */
    private $firstName;
    /** @var string */
    private $middleName;
    /** @var string */
    private $mobilePhone;
    /** @var string */
    private $homePhone;
    /** @var int */
    private $paymentStatusId;
    /** @var int */
    private $paymentId;
    /** @var string */
    private $paymentDetail;
    /** @var int */
    private $certificateNumber;
    /** @var int */
    private $certificatePin;
    /** @var int */
    private $sum;
    /** @var bool */
    private $isDelivery;
    /** @var bool */
    private $isPaidDelivery;
    /** @var int */
    private $deliveryTypeId;
    /** @var \DateTime */
    private $deliveredAt;
    /** @var int */
    private $storeId;
    /** @var int */
    private $shopId;
    /** @var int */
    private $cityId;
    /** @var int */
    private $regionId;
    /** @var int */
    private $addressId;
    /** @var string */
    private $address;
    /** @var string */
    private $zipCode;
    /** @var bool */
    private $isGift;
    /** @var bool */
    private $isSmsAlert = false;
    /** @var string */
    private $bill;
    /** @var string */
    private $comment;
    /** @var string */
    private $ipAddress;
    /** @var \DateTime */
    private $createdAt;
    /** @var \DateTime */
    private $updatedAt;
    /** @var \Model\Order\Interval\Entity */
    private $interval;
    /** @var \Model\Region\Entity */
    private $city;
    /** @var \Model\User\Entity */
    private $user;
    /** @var \Model\Order\Product\Entity[] */
    private $product = array();
    /** @var \Model\Order\Service\Entity[] */
    private $service = array();
    /** @var \Model\Order\Delivery\Entity[] */
    private $delivery = array();
    /** @var int */
    private $paySum;

    /**
     * @param array $data
     */
    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('status_id', $data)) $this->setStatusId($data['status_id']);
        if (array_key_exists('number', $data)) $this->setNumber($data['number']);
        if (array_key_exists('user_id', $data)) $this->setUserId($data['user_id']);
        if (array_key_exists('is_legal', $data)) $this->setIsLegal($data['is_legal']);
        if (array_key_exists('last_name', $data)) $this->setLastName($data['last_name']);
        if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
        if (array_key_exists('middle_name', $data)) $this->setMiddleName($data['middle_name']);
        if (array_key_exists('phone', $data)) $this->setHomePhone($data['phone']);
        if (array_key_exists('mobile', $data)) $this->setMobilePhone($data['mobile']);
        if (array_key_exists('payment_status_id', $data)) $this->setPaymentStatusId($data['payment_status_id']);
        if (array_key_exists('payment_id', $data)) $this->setPaymentId($data['payment_id']);
        if (array_key_exists('payment_detail', $data)) $this->setPaymentDetail($data['payment_detail']);
        if (array_key_exists('certificate', $data)) $this->setCertificateNumber($data['certificate']);
        if (array_key_exists('certificate_pin', $data)) $this->setCertificatePin($data['certificate_pin']);
        if (array_key_exists('sum', $data)) $this->setSum($data['sum']);
        if (array_key_exists('is_delivery', $data)) $this->setIsDelivery($data['is_delivery']);
        if (array_key_exists('is_paid_delivery', $data)) $this->setIsPaidDelivery($data['is_paid_delivery']);
        if (array_key_exists('delivery_type_id', $data)) $this->setDeliveryTypeId($data['delivery_type_id']);
        if (array_key_exists('delivery_date', $data) && $data['delivery_date'] && ('0000-00-00' != $data['delivery_date'])) {
            try {
                $this->setDeliveredAt(new \DateTime($data['delivery_date']));
            } catch(\Exception $e) {
                \App::logger()->error($e);
            }
        }
        if (array_key_exists('store_id', $data)) $this->setStoreId($data['store_id']);
        if (array_key_exists('shop_id', $data)) $this->setShopId($data['shop_id']);
        if (array_key_exists('address_id', $data)) $this->setAddressId($data['address_id']);
        if (array_key_exists('geo_id', $data)) $this->setCityId($data['geo_id']);
        if (array_key_exists('region_id', $data)) $this->setRegionId($data['region_id']);
        if (array_key_exists('address', $data)) $this->setAddress($data['address']);
        if (array_key_exists('zip_code', $data)) $this->setZipCode($data['zip_code']);
        if (array_key_exists('is_gift', $data)) $this->setIsGift($data['is_gift']);
        if (array_key_exists('is_receive_sms', $data)) $this->setIsSmsAlert($data['is_receive_sms']);
        if (array_key_exists('bill', $data)) $this->setBill($data['bill']);
        if (array_key_exists('extra', $data)) $this->setComment($data['extra']);
        if (array_key_exists('ip', $data)) $this->setIpAddress($data['ip']);
        if (array_key_exists('added', $data) && $data['added'] && ('0000-00-00' != $data['added'])) {
            try {
                $this->setCreatedAt(new \DateTime($data['added']));
            } catch(\Exception $e) {
                \App::logger()->error($e);
            }
        }
        if (array_key_exists('updated', $data) && $data['updated'] && ('0000-00-00' != $data['updated'])) {
            try {
                $this->setUpdatedAt(new \DateTime($data['updated']));
            } catch(\Exception $e) {
                \App::logger()->error($e);
            }
        }
        if (array_key_exists('interval', $data) && (bool)$data['interval']) $this->setInterval(new Interval\Entity($data['interval']));
        if (array_key_exists('geo', $data) && (bool)$data['geo']) $this->setCity(new \Model\Region\Entity($data['geo']));
        //if (array_key_exists('user', $data) && (bool)$data['user']) $this->setCity(new \Model\User\Entity($data['user']));
        if (array_key_exists('product', $data)) {
            $this->product = array();
            foreach ((array)$data['product'] as $productData) {
                $this->addProduct(new Product\Entity($productData));
            }
        }
        if (array_key_exists('service', $data)) {
            $this->service = array();
            foreach ((array)$data['service'] as $serviceData) {
                $this->addService(new Service\Entity($serviceData));
            }
        }
        if (array_key_exists('delivery', $data)) {
            $this->delivery = array();
            foreach ((array)$data['delivery'] as $deliveryData) {
                $this->addDelivery(new Delivery\Entity($deliveryData));
            }
        }
        if (array_key_exists('pay_sum', $data)) $this->setPaySum($data['pay_sum']);
    }

    /**
     * @param string $address
     */
    public function setAddress($address) {
        $this->address = (string)$address;
    }

    /**
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param int $addressId
     */
    public function setAddressId($addressId) {
        $this->addressId = $addressId ? (int)$addressId : null;
    }

    /**
     * @return int
     */
    public function getAddressId() {
        return $this->addressId;
    }

    /**
     * @param string $bill
     */
    public function setBill($bill) {
        $this->bill = $bill ? (string)$bill : null;
    }

    /**
     * @return string
     */
    public function getBill() {
        return $this->bill;
    }

    /**
     * @param int $certificateNumber
     */
    public function setCertificateNumber($certificateNumber) {
        $this->certificateNumber = $certificateNumber ? (int)$certificateNumber : null;
    }

    /**
     * @return int
     */
    public function getCertificateNumber() {
        return $this->certificateNumber;
    }

    /**
     * @param int $certificatePin
     */
    public function setCertificatePin($certificatePin) {
        $this->certificatePin = $certificatePin ? (int)$certificatePin : null;
    }

    /**
     * @return int
     */
    public function getCertificatePin() {
        return $this->certificatePin;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment) {
        $this->comment = (string)$comment;
    }

    /**
     * @return string
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null) {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $deliveredAt
     */
    public function setDeliveredAt(\DateTime $deliveredAt = null) {
        $this->deliveredAt = $deliveredAt;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveredAt() {
        return $this->deliveredAt;
    }

    /**
     * @param \Model\Order\Delivery\Entity[] $deliveries
     */
    public function setDelivery(array $deliveries = array()) {
        $this->delivery = array();
        foreach ($deliveries as $delivery) {
            $this->addDelivery($delivery);
        }
    }

    /**
     * @param Delivery\Entity $delivery
     */
    public function addDelivery(\Model\Order\Delivery\Entity $delivery) {
        $this->delivery[] = $delivery;
    }

    /**
     * @return \Model\Order\Delivery\Entity[]
     */
    public function getDelivery() {
        return $this->delivery;
    }

    /**
     * @param int $deliveryTypeId
     */
    public function setDeliveryTypeId($deliveryTypeId) {
        $this->deliveryTypeId = $deliveryTypeId ? (int)$deliveryTypeId : null;
    }

    /**
     * @return int
     */
    public function getDeliveryTypeId() {
        return $this->deliveryTypeId;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName ? (string)$firstName : null;
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @param string $homePhone
     */
    public function setHomePhone($homePhone) {
        $this->homePhone = $homePhone ? (string)$homePhone : null;
    }

    /**
     * @return string
     */
    public function getHomePhone() {
        return $this->homePhone;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param \Model\Order\Interval\Entity $interval
     */
    public function setInterval(\Model\Order\Interval\Entity $interval = null) {
        $this->interval = $interval;
    }

    /**
     * @return \Model\Order\Interval\Entity
     */
    public function getInterval() {
        return $this->interval;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress) {
        $this->ipAddress = $ipAddress ? (string)$ipAddress : null;
    }

    /**
     * @return string
     */
    public function getIpAddress() {
        return $this->ipAddress;
    }

    /**
     * @param bool $isDelivery
     */
    public function setIsDelivery($isDelivery) {
        $this->isDelivery = (bool)$isDelivery;
    }

    /**
     * @return bool
     */
    public function getIsDelivery() {
        return $this->isDelivery;
    }

    /**
     * @param bool $isGift
     */
    public function setIsGift($isGift) {
        $this->isGift = (bool)$isGift;
    }

    /**
     * @return bool
     */
    public function getIsGift() {
        return $this->isGift;
    }

    /**
     * @param bool $isLegal
     */
    public function setIsLegal($isLegal) {
        $this->isLegal = (bool)$isLegal;
    }

    /**
     * @return bool
     */
    public function getIsLegal() {
        return $this->isLegal;
    }

    /**
     * @param bool $isPaidDelivery
     */
    public function setIsPaidDelivery($isPaidDelivery) {
        $this->isPaidDelivery = (bool)$isPaidDelivery;
    }

    /**
     * @return bool
     */
    public function getIsPaidDelivery() {
        return $this->isPaidDelivery;
    }

    /**
     * @param bool $isSmsAlert
     */
    public function setIsSmsAlert($isSmsAlert) {
        $this->isSmsAlert = (bool)$isSmsAlert;
    }

    /**
     * @return bool
     */
    public function getIsSmsAlert() {
        return $this->isSmsAlert;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName ? (string)$lastName : null;
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName) {
        $this->middleName = $middleName ? (string)$middleName : null;
    }

    /**
     * @return string
     */
    public function getMiddleName() {
        return $this->middleName;
    }

    /**
     * @param string $mobilePhone
     */
    public function setMobilePhone($mobilePhone) {
        $this->mobilePhone = $mobilePhone ? (string)$mobilePhone : null;
    }

    /**
     * @return string
     */
    public function getMobilePhone() {
        return $this->mobilePhone;
    }

    /**
     * @param string $number
     */
    public function setNumber($number) {
        $this->number = (string)$number;
    }

    /**
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * @param int $paySum
     */
    public function setPaySum($paySum) {
        $this->paySum = $paySum;
    }

    /**
     * @return int
     */
    public function getPaySum() {
        return $this->paySum;
    }

    /**
     * @param string $paymentDetail
     */
    public function setPaymentDetail($paymentDetail) {
        $this->paymentDetail = (string)$paymentDetail;
    }

    /**
     * @return string
     */
    public function getPaymentDetail() {
        return $this->paymentDetail;
    }

    /**
     * @param int $paymentId
     */
    public function setPaymentId($paymentId) {
        $this->paymentId = $paymentId ? (int)$paymentId : null;
    }

    /**
     * @return int
     */
    public function getPaymentId() {
        return $this->paymentId;
    }

    /**
     * @param int $paymentStatusId
     */
    public function setPaymentStatusId($paymentStatusId) {
        $this->paymentStatusId = $paymentStatusId ? (int)$paymentStatusId : null;
    }

    /**
     * @return int
     */
    public function getPaymentStatusId() {
        return $this->paymentStatusId;
    }

    /**
     * @param \Model\Order\Product\Entity[] $products
     */
    public function setProduct(array $products) {
        $this->product = array();
        foreach ($products as $product) {
            $this->addProduct($product);
        }
    }

    /**\
     * @param Product\Entity $product
     */
    public function addProduct(\Model\Order\Product\Entity $product) {
        $this->product[] = $product;
    }

    /**
     * @return \Model\Order\Product\Entity[]
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * @param \Model\Region\Entity $city
     */
    public function setCity(\Model\Region\Entity $city = null) {
        $this->city = $city;
    }

    /**
     * @return \Model\Region\Entity
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * @param \Model\Order\Service\Entity[] $services
     */
    public function setService(array $services) {
        $this->service = array();
        foreach ($services as $service) {
            $this->addService($service);
        }
    }

    /**
     * @param Service\Entity $service
     */
    public function addService(\Model\Order\Service\Entity $service) {
        $this->service[] = $service;
    }

    /**
     * @return \Model\Order\Service\Entity[]
     */
    public function getService() {
        return $this->service;
    }

    /**
     * @param int $shopId
     */
    public function setShopId($shopId) {
        $this->shopId = $shopId ? (int)$shopId : null;
    }

    /**
     * @return int
     */
    public function getShopId() {
        return $this->shopId;
    }

    /**
     * @param int $statusId
     */
    public function setStatusId($statusId) {
        $this->statusId = (int)$statusId;
    }

    /**
     * @return int
     */
    public function getStatusId() {
        return $this->statusId;
    }

    /**
     * @param int $storeId
     */
    public function setStoreId($storeId) {
        $this->storeId = $storeId ? (int)$storeId : null;
    }

    /**
     * @return int
     */
    public function getStoreId() {
        return $this->storeId;
    }

    /**
     * @param int $sum
     */
    public function setSum($sum) {
        $this->sum = $sum;
    }

    /**
     * @return int
     */
    public function getSum() {
        return $this->sum;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId) {
        $this->typeId = (int)$typeId;
    }

    /**
     * @return int
     */
    public function getTypeId() {
        return $this->typeId;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null) {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * @param \Model\User\Entity $user
     */
    public function setUser(\Model\User\Entity $user = null) {
        $this->user = $user;
    }

    /**
     * @return \Model\User\Entity
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId) {
        $this->userId = $userId ? (int)$userId : null;
    }

    /**
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode) {
        $this->zipCode = (string)$zipCode;
    }

    /**
     * @return string
     */
    public function getZipCode() {
        return $this->zipCode;
    }

    /**
     * @param int $regionId
     */
    public function setRegionId($regionId) {
        $this->regionId = $regionId ? (int)$regionId : null;
    }

    /**
     * @return int
     */
    public function getRegionId() {
        return $this->regionId;
    }

    /**
     * @param int $cityId
     */
    public function setCityId($cityId) {
        $this->cityId = $cityId ? (int)$cityId : null;
    }

    /**
     * @return int
     */
    public function getCityId() {
        return $this->cityId;
    }
}