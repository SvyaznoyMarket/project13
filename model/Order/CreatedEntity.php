<?php

namespace Model\Order;

class CreatedEntity {
    /** @var int */
    private $id;
    /** @var bool */
    private $confirmed;
    /** @var string */
    private $number;
    /** @var int */
    private $userId;
    /** @var int */
    private $sum;
    /** @var float */
    private $paySum;
    /** @var string */
    private $paymentUrl;
    /** @var \DateTime */
    private $deliveredAt;
    /** @var string */
    private $accessToken;
    /** @var string */
    public $numberErp;
    /** @var int */
    public $paymentId;
    /** @var int|null */
    public $prepaidSum;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('number', $data)) $this->setNumber($data['number']);
        if (array_key_exists('user_id', $data)) $this->setUserId($data['user_id']);
        if (array_key_exists('access_token', $data)) $this->setAccessToken($data['access_token']);
        if (array_key_exists('confirmed', $data)) $this->setConfirmed($data['confirmed']);
        if (array_key_exists('price', $data)) $this->setSum($data['price']);
        if (array_key_exists('pay_sum', $data)) $this->setPaySum($data['pay_sum']);
        if (array_key_exists('payment_url', $data)) $this->setPaymentUrl($data['payment_url']);
        if (array_key_exists('payment_id', $data)) $this->setPaymentId($data['payment_id']);
        if (array_key_exists('delivery_date', $data) && $data['delivery_date'] && ('0000-00-00' != $data['delivery_date'])) {
            try {
                $this->setDeliveredAt(new \DateTime($data['delivery_date']));
            } catch(\Exception $e) {
                \App::logger()->error($e);
            }
        }
        if (array_key_exists('number_erp', $data)) $this->numberErp = (string)$data['number_erp'];
        if (!empty($data['meta_data']['prepaid_sum'])) $this->prepaidSum = (float)$data['meta_data']['prepaid_sum'];
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed($confirmed) {
        $this->confirmed = in_array($confirmed, ["true", 1, true]);
    }

    /**
     * @return boolean
     */
    public function getConfirmed() {
        return $this->confirmed;
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
     * @param float $paySum
     */
    public function setPaySum($paySum) {
        $this->paySum = floatval($paySum);
    }

    /**
     * @return float
     */
    public function getPaySum() {
        return $this->paySum;
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
     * @param int $sum
     */
    public function setSum($sum) {
        $this->sum = (int)$sum;
    }

    /**
     * @return int
     */
    public function getSum() {
        return $this->sum;
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
}