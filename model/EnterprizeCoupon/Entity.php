<?php

namespace Model\EnterprizeCoupon;

class Entity {
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var string */
    private $image;
    /** @var string */
    private $price;
    /** @var bool */
    private $isCurrency;
    /** @var string */
    private $backgroundImage;
    /** @var int */
    private $minOrderSum;
    /** @var \DateTime|null */
    private $startDate;
    /** @var \DateTime|null */
    private $endDate;
    /** @var string */
    private $link;
    /** @var string */
    private $linkName;
    /**
     * Только информационная ссылка без необходимости заполнения enterprize-формы
     * @var bool
     */
    private $isInformationOnly;
    /**
     * Для участников enterprize?
     * @var bool
     */
    private $isForMember = false;
    /**
     * Для неучастников enterprize?
     * @var bool
     */
    private $isForNotMember = true;
    /**
     * Токен странички в wordpress-е
     * @var string
     */
    private $descriptionToken;

    public function __construct(array $data = []) {
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('image', $data)) $this->setImage($data['image']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('isCurrency', $data)) $this->setIsCurrency($data['isCurrency']);
        if (array_key_exists('backgroundImage', $data)) $this->setBackgroundImage($data['backgroundImage']);
        if (array_key_exists('minOrderSum', $data)) $this->setMinOrderSum($data['minOrderSum']);
        if (array_key_exists('startDate', $data)) $this->setStartDate($data['startDate'] ? new \DateTime($data['startDate']) : null);
        if (array_key_exists('endDate', $data)) $this->setEndDate($data['endDate'] ? new \DateTime($data['endDate']) : null);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('linkName', $data)) $this->setLinkName($data['linkName']);
        if (array_key_exists('isInformationOnly', $data)) $this->setIsInformationOnly($data['isInformationOnly']);
        if (array_key_exists('isForMember', $data)) $this->setIsForMember($data['isForMember']);
        if (array_key_exists('isForNotMember', $data)) $this->setIsForNotMember($data['isForNotMember']);
        if (array_key_exists('descriptionToken', $data)) $this->setDescriptionToken($data['descriptionToken']);
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $token
     */
    public function setToken($token) {
        $this->token = (string)$token;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param string $image
     */
    public function setImage($image) {
        $this->image = (string)$image;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param string $price
     */
    public function setPrice($price) {
        $this->price = (string)$price;
    }

    /**
     * @return string
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param boolean $isCurrency
     */
    public function setIsCurrency($isCurrency) {
        $this->isCurrency = (bool)$isCurrency;
    }

    /**
     * @return boolean
     */
    public function getIsCurrency() {
        return $this->isCurrency;
    }

    /**
     * @param string $backgroundImage
     */
    public function setBackgroundImage($backgroundImage) {
        $this->backgroundImage = (string)$backgroundImage;
    }

    /**
     * @return string
     */
    public function getBackgroundImage() {
        return $this->backgroundImage;
    }

    /**
     * @param int $minOrderSum
     */
    public function setMinOrderSum($minOrderSum) {
        $this->minOrderSum = (int)$minOrderSum;
    }

    /**
     * @return int
     */
    public function getMinOrderSum() {
        return $this->minOrderSum;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate = null) {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate = null) {
        $this->endDate = $endDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param string $link
     */
    public function setLink($link) {
        $this->link = (string)$link;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param string $linkName
     */
    public function setLinkName($linkName) {
        $this->linkName = (string)$linkName;
    }

    /**
     * @return string
     */
    public function getLinkName() {
        return $this->linkName;
    }

    /**
     * @param string $descriptionToken
     */
    public function setDescriptionToken($descriptionToken) {
        $this->descriptionToken = (bool)$descriptionToken;
    }

    /**
     * @return string
     */
    public function getDescriptionToken() {
        return $this->descriptionToken;
    }

    /**
     * @param boolean $isForNotMemberOnly
     */
    public function setIsForMember($isForNotMemberOnly)
    {
        $this->isForMember = (bool)$isForNotMemberOnly;
    }

    /**
     * @return boolean
     */
    public function isForMember() {
        return $this->isForMember;
    }

    /**
     * @param boolean $isForNotMember
     */
    public function setIsForNotMember($isForNotMember) {
        $this->isForNotMember = (bool)$isForNotMember;
    }

    /**
     * @return boolean
     */
    public function isForNotMember() {
        return $this->isForNotMember;
    }

    /**
     * @param boolean $isInformationOnly
     */
    public function setIsInformationOnly($isInformationOnly) {
        $this->isInformationOnly = (bool)$isInformationOnly;
    }

    /**
     * @return boolean
     */
    public function isInformationOnly() {
        return $this->isInformationOnly;
    }
}