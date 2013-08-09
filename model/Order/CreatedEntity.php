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
    /** @var int */
    private $paySum;
    /** @var string */
    private $paymentUrl;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('number', $data)) $this->setNumber($data['number']);
        if (array_key_exists('user_id', $data)) $this->setUserId($data['user_id']);
        if (array_key_exists('confirmed', $data)) $this->setConfirmed($data['confirmed']);
        if (array_key_exists('price', $data)) $this->setSum($data['price']);
        if (array_key_exists('pay_sum', $data)) $this->setPaySum($data['pay_sum']);
        if (array_key_exists('payment_url', $data)) $this->setPaymentUrl($data['payment_url']);
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
     * @param int $paySum
     */
    public function setPaySum($paySum) {
        $this->paySum = (int)$paySum;
    }

    /**
     * @return int
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

}