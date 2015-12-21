<?php

namespace Model\Order;

use Model\Point\PointEntity as Point;
use Model\Shop\Entity as Shop;

class Entity {
    const TYPE_ORDER = 1;
    const TYPE_PREORDER = 2;
    const TYPE_CUSTOM = 3;
    const TYPE_SLOT = 4;
    const TYPE_1CLICK = 9;

    const STATUS_FORMED = 1;
    const STATUS_READY = 6;
    const STATUS_APPROVED_BY_CALL_CENTER = 2;
    const STATUS_FORMED_IN_STOCK = 3;
    const STATUS_IN_DELIVERY = 4;
    const STATUS_DELIVERED = 5;
    const STATUS_CANCELED = 100;

    const PAYMENT_TYPE_ID_CREDIT_CARD_ONLINE = 5;
    const PAYMENT_TYPE_ID_ONLINE_CREDIT = 6;
    const PAYMENT_TYPE_ID_PAYPAL = 13;

    const PAYMENT_STATUS_NOT_PAID = 1;  // не оплачен
    const PAYMENT_STATUS_TRANSFER = 4;  // начало оплаты
    const PAYMENT_STATUS_ADVANCE = 3;   // частично оплачен
    const PAYMENT_STATUS_PAID = 2;      // оплачен
    const PAYMENT_STATUS_CANCELED = 5;  // отмена оплаты
    const PAYMENT_STATUS_SVYAZNOY_CLUB = 21;  // оплата баллами Связной-клуб

    /** @var int */
    public $id;
    /** @var string */
    public $ui;
    /** @var int */
    public $typeId;
    /**
     * @deprecated
     * @var int
     */
    public $statusId;
    /** @var StatusEntity|null */
    public $status;
    /** @var string */
    public $number;
    /** @var string */
    public $numberErp;
    /** @var int */
    public $userId;
    /** @var bool */
    public $isLegal;
    /** @var string */
    public $lastName;
    /** @var string */
    public $firstName;
    /** @var string */
    public $middleName;
    /** @var string */
    public $mobilePhone;
    /** @var string */
    public $homePhone;
    /** @var int */
    public $paymentStatusId;
    /** @var PaymentStatusEntity|null */
    public $paymentStatus;
    /** @var int */
    public $paymentId;
    /** @var string */
    public $paymentDetail;
    /** @var int */
    public $certificateNumber;
    /** @var int */
    public $certificatePrice;
    /** @var int */
    public $certificatePin;
    /** @var bool */
    public $isDelivery;
    /** @var bool */
    public $isPaidDelivery;
    /** @var int */
    public $deliveryTypeId;
    /** @var \DateTime */
    public $deliveredAt;
    /** @var array */
    public $deliveryDateInterval;
    /** @var int */
    public $storeId;
    /** @var int */
    public $shopId;
    /** @var int */
    public $cityId;
    /** @var int */
    public $regionId;
    /** @var int */
    public $addressId;
    /** @var string */
    public $address;
    /** @var string */
    public $zipCode;
    /** @var bool */
    public $isGift;
    /** @var bool */
    public $isSmsAlert = false;
    /** @var string */
    public $bill;
    /** @var string */
    public $comment;
    /** @var string */
    public $ipAddress;
    /** @var \DateTime */
    public $createdAt;
    /** @var \DateTime */
    public $updatedAt;
    /** @var \Model\Order\Interval\Entity */
    public $interval;
    /** @var \Model\Region\Entity */
    public $city;
    /** @var \Model\User\Entity */
    public $user;
    /** @var \Model\Order\Product\Entity[] */
    public $product = [];
    /** @var \Model\Order\Delivery\Entity[] */
    public $delivery = [];
    /**
     * Сумма без скидок
     * @var int
     */
    public $sum;
    /**
     * Сумма со скидками, но без скидки за онлайн оплату
     * @var float
     */
    public $paySum;
    /**
     * Сумма со скидками и со спидкой за онлайн оплату. Заполнена только, если была произведена онлайн оплата.
     * @var float
     */
    public $paySumWithOnlineDiscount;
    /**
     * self::$paySumWithOnlineDiscount или self::$paySum
     * @var float
     */
    public $totalPaySum;
    /** @var int */
    public $discountSum;
    /** @var Credit\Entity|null */
    public $credit;
    /** @var int */
    public $subway_id;
    /** @var string */
    public $paymentUrl;
    /** @var int */
    public $couponNumber;
    /** @var bool */
    public $isPartner;
    /** @var array */
    public $meta_data = [];
    /** @var string|null */
    public $email;
    /** @var Seller|null */
    public $seller;
    /** @var string */
    private $accessToken;
    /** @var string|null */
    public $pointUi;
    /** @var Point|null */
    public $point;
    /** @var Shop|null */
    public $shop;
    /** @var string */
    public $context;
    /** @var int|null */
    public $prepaidSum;
    /** @var bool */
    public $isCancelRequestAvailable;
    /** @var array */
    public $dayRange = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (isset($data['ui'])) $this->ui = (string)$data['ui'];
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (!empty($data['status_id'])) {
            $this->setStatusId($data['status_id']);
        } else if (!empty($data['status']['id'])) {
            $this->setStatusId($data['status']['id']);
        }
        if (isset($data['status']['id'])) $this->status = new StatusEntity($data['status']);
        if (array_key_exists('access_token', $data)) $this->setAccessToken($data['access_token']);
        if (array_key_exists('number', $data)) $this->setNumber($data['number']);
        if (array_key_exists('number_erp', $data)) $this->setNumberErp($data['number_erp']);
        if (array_key_exists('user_id', $data)) $this->setUserId($data['user_id']);
        if (array_key_exists('is_legal', $data)) $this->setIsLegal($data['is_legal']);
        if (array_key_exists('last_name', $data)) $this->setLastName($data['last_name']);
        if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
        if (array_key_exists('middle_name', $data)) $this->setMiddleName($data['middle_name']);
        if (array_key_exists('phone', $data)) $this->setHomePhone($data['phone']);
        if (array_key_exists('mobile', $data)) $this->setMobilePhone($data['mobile']);
        if (array_key_exists('payment_status_id', $data)) $this->setPaymentStatusId($data['payment_status_id']);
        if (isset($data['payment_status']['id'])) $this->paymentStatus = new PaymentStatusEntity($data['payment_status']);
        if (array_key_exists('payment_id', $data)) $this->setPaymentId($data['payment_id']);
        if (array_key_exists('payment_detail', $data)) $this->setPaymentDetail($data['payment_detail']);
        if (array_key_exists('certificate', $data)) $this->setCertificateNumber($data['certificate']);
        if (array_key_exists('certificate_price', $data)) $this->setCertificatePrice($data['certificate_price']);
        if (array_key_exists('certificate_pin', $data)) $this->setCertificatePin($data['certificate_pin']);
        if (array_key_exists('sum', $data)) $this->setSum($data['sum']);
        if (array_key_exists('is_paid_delivery', $data)) $this->setIsPaidDelivery($data['is_paid_delivery']);
        if (array_key_exists('delivery_type_id', $data)) $this->setDeliveryTypeId($data['delivery_type_id']);
        if (array_key_exists('delivery_date', $data) && $data['delivery_date'] && ('0000-00-00' != $data['delivery_date'])) {
            try {
                $this->setDeliveredAt(new \DateTime($data['delivery_date']));
            } catch(\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['order']);
            }
        }

        if (isset($data['delivery'][0]['delivery_date_interval']['from']) && isset($data['delivery'][0]['delivery_date_interval']['to'])) {
            try {
                $this->deliveryDateInterval = [
                    'name' =>
                        !empty($data['delivery'][0]['delivery_date_interval']['name'])
                        ? $data['delivery'][0]['delivery_date_interval']['name']
                        : sprintf('с %s по %s', (new \DateTime($data['delivery'][0]['delivery_date_interval']['from']))->format('d.m'), (new \DateTime($data['delivery'][0]['delivery_date_interval']['to']))->format('d.m')),
                ];
            } catch (\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['order']);
            }
        } else if (\App::abTest()->isOrderWithDeliveryInterval() && $this->deliveredAt) {
            try {
                $date = clone $this->deliveredAt;

                if ($dayFrom = $date->diff((new \DateTime())->setTime(0, 0, 0))->days) {
                    $this->dayRange['from'] = ($dayFrom > 1) ? ($dayFrom - 1): $dayFrom;
                    $this->dayRange['to'] = $this->dayRange['from'] + 2;
                    $this->deliveryDateInterval = [
                        'name' => sprintf('%s-%s %s', $this->dayRange['from'], $this->dayRange['to'], \App::helper()->numberChoice($this->dayRange['to'], ['день', 'дня', 'дней'])),
                    ];
                }
            } catch (\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart.split']);
            }
        }
        if (array_key_exists('store_id', $data)) $this->setStoreId($data['store_id']);
        if (array_key_exists('shop_id', $data)) $this->setShopId($data['shop_id']);
        if (array_key_exists('point_ui', $data)) $this->pointUi = $data['point_ui'] ? (string)$data['point_ui']: null;
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
            $this->product = [];
            foreach ((array)$data['product'] as $productData) {
                $this->addProduct(new Product\Entity($productData));
            }
        }
        if (array_key_exists('delivery', $data)) {
            foreach ((array)$data['delivery'] as $deliveryData) {
                $this->addDelivery(new Delivery\Entity($deliveryData));
            }
        }
        if (array_key_exists('pay_sum', $data)) $this->setPaySum($data['pay_sum']);
        if (array_key_exists('payment_sum', $data)) $this->paySumWithOnlineDiscount = (float)$data['payment_sum'];

        if ($this->paySumWithOnlineDiscount) {
            $this->totalPaySum = $this->paySumWithOnlineDiscount;
        } else {
            $this->totalPaySum = $this->paySum;
        }

        if (array_key_exists('discount_sum', $data)) $this->setDiscountSum($data['discount_sum']);
        if (array_key_exists('credit', $data) && (bool)$data['credit']) $this->setCredit(new Credit\Entity($data['credit']));
        if (array_key_exists('subway_id', $data)) $this->setSubwayId($data['subway_id']);
        if (array_key_exists('payment_url', $data)) $this->setPaymentUrl($data['payment_url']);
        if (array_key_exists('coupon_number', $data)) $this->setCouponNumber($data['coupon_number']);
        if (array_key_exists('is_partner', $data)) $this->setIsPartner($data['is_partner']);

        if (array_key_exists('meta_data', $data) && is_array($data['meta_data'])) $this->meta_data = $data['meta_data'];
        if (array_key_exists('email', $data) && !empty($data['email'])) $this->email = (string)$data['email'];
        if (array_key_exists('seller', $data) && !empty($data['seller'])) $this->seller = new Seller($data['seller']);
        if (!empty($data['context'])) $this->context = (string)$data['context'];
        if (!empty($this->meta_data['prepaid_sum'])) $this->prepaidSum = (float)$this->meta_data['prepaid_sum'];
        if (array_key_exists('is_cancel_request_available', $data)) $this->isCancelRequestAvailable = (bool)$data['is_cancel_request_available'];
    }

    public function dump() {

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
     * @param Delivery\Entity $delivery
     */
    public function addDelivery(\Model\Order\Delivery\Entity $delivery) {
        $this->delivery[] = $delivery;
    }

    /**
     * @return \Model\Order\Delivery\Entity
     */
    public function getDelivery() {
        return reset($this->delivery);
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

    /** Возвращает номер вида XF719570
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * @param string $numberErp
     */
    public function setNumberErp($numberErp) {
        $this->numberErp = (string)$numberErp;
    }

    /** Возвращает номер вида COXF-719233
     * @return string
     */
    public function getNumberErp() {
        return $this->numberErp;
    }

    /**
     * @param float $paySum
     */
    public function setPaySum($paySum) {
        $this->paySum = (float)$paySum;
    }

    /**
     * @return float
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
        $this->product = [];
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
     * @param int $shopId
     */
    public function setShopId($shopId) {
        $this->shopId = $shopId ? (int)$shopId : null;
    }

    /**
     * @return int|null
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
     * @param int|float $sum
     */
    public function setSum($sum) {
        $this->sum = $sum;
    }

    /**
     * @return int|float
     */
    public function getSum() {
        return $this->sum;
    }

    /**
     * @param int $discountSum
     */
    public function setDiscountSum($discountSum) {
        $this->discountSum = $discountSum;
    }

    /**
     * @return int
     */
    public function getDiscountSum() {
        return $this->discountSum;
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
     * @param \DateTime|string $updatedAt
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

    /**
     * @param \Model\Order\Credit\Entity|null $credit
     */
    public function setCredit(Credit\Entity $credit) {
        $this->credit = $credit;
    }

    /**
     * @return \Model\Order\Credit\Entity|null
     */
    public function getCredit() {
        return $this->credit;
    }

    /**
     * @return int
     */
    public function getProductSum() {
        $sum = 0;
        foreach ($this->getProduct() as $product) {
            $sum += $product->getPrice() * $product->getQuantity();
        }

        return $sum;
    }

    /**
     * @param int $subwayId
     */
    public function setSubwayId($subwayId) {
        $this->subway_id = $subwayId ? (int)$subwayId : null;
    }

    /**
     * @return int
     */
    public function getSubwayId() {
        return $this->subway_id;
    }

    /**
     * @param string $paymentUrl
     */
    public function setPaymentUrl($paymentUrl) {
        $this->paymentUrl = base64_decode(trim((string)$paymentUrl));
    }

    /**
     * @return string
     */
    public function getPaymentUrl() {
        return $this->paymentUrl;
    }

    /**
     * @param int|string $number
     * @return int
     */
    public function setCouponNumber($number)
    {
        if (!is_int($number)) $number = preg_replace('/[\s]*/', "", $number);
        return $this->couponNumber = (int)$number;
    }

    /**
     * @return int
     */
    public function getCouponNumber()
    {
        return $this->couponNumber;
    }


    /**
     * @param int $isPartner
     */
    public function setIsPartner($isPartner)
    {
        $this->isPartner = (bool)$isPartner;
    }

    /**
     * @return bool
     */
    public function getIsPartner()
    {
        return $this->isPartner;
    }

    /**
     * @param int $certificatePrice
     */
    public function setCertificatePrice($certificatePrice)
    {
        $this->certificatePrice = $certificatePrice;
    }

    /**
     * @return int
     */
    public function getCertificatePrice()
    {
        return $this->certificatePrice;
    }

    /** Статус оплаты: оплачен (true) или не оплачен (false)
     * @return bool
     */
    public function isPaid() {
        return $this->paymentStatusId == self::PAYMENT_STATUS_PAID;
    }

    /**
     * @param string $acceessToken
     */
    public function setAccessToken($acceessToken) {
        $this->accessToken = (string)$acceessToken;
    }

    /**
     * @return string
     */
    public function getAccessToken() {
        return $this->accessToken;
    }

    /** Возвращает значение из метаданных по ключу
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getMetaByKey($key, $default = null) {
        return array_key_exists($key, $this->meta_data) && is_array($this->meta_data[$key])
            ? reset($this->meta_data[$key])     // возвращаем первый элемент массива (особенности ответа ядра)
            : $default;
    }

    public function getMeta() {
        return $this->meta_data;
    }

    /** Оплата с помощью баллов Связного клуба
     * @return bool
     */
    public function isPaidBySvyaznoy() {
        return $this->paymentStatusId == self::PAYMENT_STATUS_SVYAZNOY_CLUB;
    }

    /** Возвращает количество списанных баллов
     * @return float
     */
    public function getSvyaznoyPaymentSum(){
        return (float)$this->getMetaByKey('payment.svyaznoy_club', 0);
    }

    /** Является ли заказ заявкой (например, заказ кухонь)
     * @return bool
     */
    public function isSlot(){
        return $this->typeId == self::TYPE_SLOT;
    }

    /** Оформлен ли заказ в кредит
     * @return bool
     */
    public function isCredit(){
        return $this->paymentId == self::PAYMENT_TYPE_ID_ONLINE_CREDIT;
    }
}

class Seller {

    public $id;
    public $ui;
    public $name;

    public function __construct(array $data = []) {
        if (isset($data['id'])) $this->id = (int)$data['id'];
        if (isset($data['ui'])) $this->ui = (string)$data['ui'];
        if (isset($data['name'])) $this->name = (string)$data['name'];
    }
}