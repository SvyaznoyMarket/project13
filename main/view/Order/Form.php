<?php

namespace View\Order;

class Form {
    /** @var int */
    private $deliveryTypeId;
    /** @var int */
    private $paymentMethodId;
    /** @var string */
    private $firstName;
    /** @var string */
    private $lastName;
    /** @var string */
    private $mobilePhone;
    /** @var string */
    private $email;
    /** @var string */
    private $addressStreet;
    /** @var int */
    private $subwayId;
    /** @var string */
    private $addressNumber;
    /** @var string */
    private $addressBuilding;
    /** @var string */
    private $addressApartment;
    /** @var string */
    private $addressFloor;
    /** @var string */
    private $comment;
    /** @var int */
    private $creditBankId;
    /** @var string */
    private $bonusCardnumber;
    /** @var int */
    private $bonusCardId;
    /** @var string */
    private $certificateCardnumber;
    /** @var string */
    private $certificatePin;
    /** @var bool */
    private $qiwiPhone;
    /** @var bool */
    private $agreed;
    /** @var bool */
    private $oneClick;

    /** @var array */
    private $errors = array(
        'global'                 => null,
        'delivery_type_id'       => null,
        'recipient_first_name'   => null,
        'recipient_last_name'    => null,
        'recipient_phonenumbers' => null,
        'recipient_email'        => null,
        'address_street'         => null,
        'address_number'         => null,
        'address_building'       => null,
        'address_apartment'      => null,
        'address_floor'          => null,
        'extra'                  => null,
        'credit_bank_id'         => null,
        'bonus_card_number'      => null,
        'bonus_card_id'   => null,
        'payment_method_id'      => null,
        'agreed'                 => null,
        'cardnumber'             => null,
        'cardpin'                => null,
        'subway_id'              => null,
        'qiwi_phone'             => null,
        'one_click'              => null,
    );

    public function __construct(array $data = []) {
        $this->fromArray($data);
    }

    public function fromArray(array $data) {
        if (array_key_exists('delivery_type_id', $data))       $this->setDeliveryTypeId($data['delivery_type_id']);
        if (array_key_exists('recipient_first_name', $data))   $this->setFirstName($data['recipient_first_name']);
        if (array_key_exists('recipient_last_name', $data))    $this->setLastName($data['recipient_last_name']);
        if (array_key_exists('recipient_phonenumbers', $data)) $this->setMobilePhone($data['recipient_phonenumbers']);
        if (array_key_exists('recipient_email', $data))        $this->setEmail($data['recipient_email']);
        if (array_key_exists('address_street', $data))         $this->setAddressStreet($data['address_street']);
        if (array_key_exists('address_number', $data))         $this->setAddressNumber($data['address_number']);
        if (array_key_exists('address_building', $data))       $this->setAddressBuilding($data['address_building']);
        if (array_key_exists('address_apartment', $data))      $this->setAddressApartment($data['address_apartment']);
        if (array_key_exists('address_floor', $data))          $this->setAddressFloor($data['address_floor']);
        if (array_key_exists('extra', $data))                  $this->setComment($data['extra']);
        if (array_key_exists('credit_bank_id', $data))         $this->setCreditBankId($data['credit_bank_id']);
        if (array_key_exists('bonus_card_number', $data))      $this->setBonusCardnumber($data['bonus_card_number']);
        if (array_key_exists('bonus_card_id', $data))          $this->setBonusCardId($data['bonus_card_id']);
        if (array_key_exists('payment_method_id', $data))      $this->setPaymentMethodId($data['payment_method_id']);
        if (array_key_exists('agreed', $data))                 $this->setAgreed($data['agreed']);
        if (array_key_exists('cardnumber', $data))             $this->setCertificateCardnumber($data['cardnumber']);
        if (array_key_exists('cardpin', $data))                $this->setCertificatePin($data['cardpin']);
        if (array_key_exists('subway_id', $data))              $this->setSubwayId($data['subway_id']);
        if (array_key_exists('qiwi_phone', $data))             $this->setQiwiPhone($data['qiwi_phone']);
        if (array_key_exists('one_click', $data))              $this->setOneClick($data['one_click']);
    }

    /**
     * @param string $addressApartment
     */
    public function setAddressApartment($addressApartment) {
        $this->addressApartment = trim((string)$addressApartment);
    }

    /**
     * @return string
     */
    public function getAddressApartment() {
        return $this->addressApartment;
    }

    /**
     * @param string $addressBuilding
     */
    public function setAddressBuilding($addressBuilding) {
        $this->addressBuilding = trim((string)$addressBuilding);
    }

    /**
     * @return string
     */
    public function getAddressBuilding() {
        return $this->addressBuilding;
    }

    /**
     * @param string $addressFloor
     */
    public function setAddressFloor($addressFloor) {
        $this->addressFloor = trim((string)$addressFloor);
    }

    /**
     * @return string
     */
    public function getAddressFloor() {
        return $this->addressFloor;
    }

    /**
     * @param string $addressNumber
     */
    public function setAddressNumber($addressNumber) {
        $this->addressNumber = trim((string)$addressNumber);
    }

    /**
     * @return string
     */
    public function getAddressNumber() {
        return $this->addressNumber;
    }

    /**
     * @param string $addressStreet
     */
    public function setAddressStreet($addressStreet) {
        $this->addressStreet = trim((string)$addressStreet);
    }

    /**
     * @return string
     */
    public function getAddressStreet() {
        return $this->addressStreet;
    }

    /**
     * @param boolean $agreed
     */
    public function setAgreed($agreed) {
        $this->agreed = (bool)$agreed;
    }

    /**
     * @return boolean
     */
    public function getAgreed() {
        return $this->agreed;
    }

    /**
     * @param string $certificateCardnumber
     */
    public function setCertificateCardnumber($certificateCardnumber) {
        $this->certificateCardnumber = trim((string)$certificateCardnumber);
    }

    /**
     * @return string
     */
    public function getCertificateCardnumber() {
        return $this->certificateCardnumber;
    }

    /**
     * @param string $certificatePin
     */
    public function setCertificatePin($certificatePin) {
        $this->certificatePin = trim((string)$certificatePin);
    }

    /**
     * @return string
     */
    public function getCertificatePin() {
        return $this->certificatePin;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment) {
        $this->comment = trim((string)$comment);
    }

    /**
     * @return string
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @param int $creditBankId
     */
    public function setCreditBankId($creditBankId) {
        $this->creditBankId = $creditBankId ? (int)$creditBankId : null;
    }

    /**
     * @return int
     */
    public function getCreditBankId() {
        return $this->creditBankId;
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
        $this->firstName = trim((string)$firstName);
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = trim((string)$lastName);
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @param string $mobilePhone
     */
    public function setMobilePhone($mobilePhone) {
        $this->mobilePhone = trim((string)$mobilePhone);
    }

    /**
     * @return string
     */
    public function getMobilePhone() {
        return $this->mobilePhone;
    }

    /**
     * @param int $paymentMethodId
     */
    public function setPaymentMethodId($paymentMethodId) {
        $this->paymentMethodId = $paymentMethodId ? (int)$paymentMethodId : null;
    }

    /**
     * @return int
     */
    public function getPaymentMethodId() {
        return $this->paymentMethodId;
    }

    /**
     * @param string $bonusCardnumber
     */
    public function setBonusCardnumber($bonusCardnumber) {
        $this->bonusCardnumber = str_replace(' ','', (string)$bonusCardnumber);
    }

    /**
     * @return string
     */
    public function getBonusCardnumber() {
        return $this->bonusCardnumber;
    }

    /**
     * @param int $bonusCardId
     */
    public function setBonusCardId($bonusCardId) {
        $this->bonusCardId = $bonusCardId;
    }

    /**
     * @return int
     */
    public function getBonusCardId() {
        return $this->bonusCardId;
    }

    /**
     * @param int $subwayId
     */
    public function setSubwayId($subwayId) {
        $this->subwayId = $subwayId ? (int)$subwayId : null;
    }

    /**
     * @return int
     */
    public function getSubwayId() {
        return $this->subwayId;
    }

    /**
     * @return bool
     */
    public function hasSubway() {
        return \App::user()->getRegion()->getHasSubway();
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = trim((string)$email);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $qiwiPhone
     */
    public function setQiwiPhone($qiwiPhone) {
        $this->qiwiPhone = trim((string)$qiwiPhone);
    }

    /**
     * @return string
     */
    public function getQiwiPhone() {
        return $this->qiwiPhone;
    }

    /**
     * @param $name
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setError($name, $value) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        $this->errors[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getError($name) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        return $this->errors[$name];
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid() {
        $isValid = true;
        foreach ($this->errors as $error) {
            if (null !== $error) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }

    /**
     * @param boolean $oneClick
     */
    public function setOneClick($oneClick) {
        $this->oneClick = (bool)$oneClick;
    }

    /**
     * @return bool
     */
    public function getOneClick() {
        return $this->oneClick;
    }

}