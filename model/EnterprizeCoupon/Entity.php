<?php

namespace Model\EnterprizeCoupon;

class Entity {
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var string */
    private $image;
    /** @var float */
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
    /** @var int */
    private $countLimit;
    /** @var string */
    private $segmentDescription;
    /** @var string */
    private $partner;
    /** @var string */
    private $partnerUrl;
    /** @var string */
    private $partnerImageUrl;
    /** @var string */
    private $partnerDescription;
    /** @var string */
    private $partnerKeyword;
    /** @var \Model\EnterprizeCoupon\DiscountCoupon\Entity|null */
    private $discount;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        $this->fromScms($data);
    }

    /**
     * Подгрузка данных полученых от http://cms.enter.ru/v1/enterprize/coupon-type.json
     * @param array $data
     */
    public function fromCms(array $data = []) {
        if (array_key_exists('token', $data))               $this->setToken($data['token']);
        if (array_key_exists('name', $data))                $this->setName($data['name']);
        if (array_key_exists('image', $data))               $this->setImage($data['image']);
        if (array_key_exists('price', $data))               $this->setPrice($data['price']);
        if (array_key_exists('isCurrency', $data))          $this->setIsCurrency($data['isCurrency']);
        if (array_key_exists('backgroundImage', $data))     $this->setBackgroundImage($data['backgroundImage']);
        if (array_key_exists('minOrderSum', $data))         $this->setMinOrderSum($data['minOrderSum']);
        if (array_key_exists('startDate', $data))           $this->setStartDate($data['startDate'] ? new \DateTime($data['startDate']) : null);
        if (array_key_exists('endDate', $data))             $this->setEndDate($data['endDate'] ? new \DateTime($data['endDate']) : null);
        if (array_key_exists('link', $data))                $this->setLink($data['link']);
        if (array_key_exists('linkName', $data))            $this->setLinkName($data['linkName']);
        if (array_key_exists('isInformationOnly', $data))   $this->setIsInformationOnly($data['isInformationOnly']);
        if (array_key_exists('isForMember', $data))         $this->setIsForMember($data['isForMember']);
        if (array_key_exists('isForNotMember', $data))      $this->setIsForNotMember($data['isForNotMember']);
        if (array_key_exists('descriptionToken', $data))    $this->setDescriptionToken($data['descriptionToken']);
    }

    /**
     * Подгрузка данных полученых от http://scms.enter.ru/v2/coupon/get
     * @param array $data
     */
    public function fromScms(array $data = []) {
        if (array_key_exists('uid', $data))                     $this->setToken($data['uid']);
        if (array_key_exists('segment', $data))                 $this->setName($data['segment']);
        if (array_key_exists('segment_image_url', $data))       $this->setImage($data['segment_image_url']);
        if (array_key_exists('value', $data))                   $this->setPrice($data['value']);
        if (array_key_exists('is_currency', $data))             $this->setIsCurrency($data['is_currency']);
        if (array_key_exists('background_image_url', $data))    $this->setBackgroundImage($data['background_image_url']);
        if (array_key_exists('min_order_sum', $data))           $this->setMinOrderSum($data['min_order_sum']);
        if (array_key_exists('start_date', $data))              $this->setStartDate($data['start_date'] ? new \DateTime($data['start_date']) : null);
        if (array_key_exists('end_date', $data))                $this->setEndDate($data['end_date'] ? new \DateTime($data['end_date']) : null);
        if (array_key_exists('segment_url', $data))             $this->setLink($data['segment_url']);
        if (array_key_exists('is_for_member', $data))           $this->setIsForMember($data['is_for_member']);
        if (array_key_exists('is_for_not_member', $data))       $this->setIsForNotMember($data['is_for_not_member']);
        if (array_key_exists('count_limit', $data))             $this->setCountLimit($data['count_limit']);
        if (array_key_exists('segment_description', $data))     $this->setSegmentDescription($data['segment_description']);
        if (array_key_exists('partner', $data))                 $this->setPartner($data['partner']);
        if (array_key_exists('partner_url', $data))             $this->setPartnerUrl($data['partner_url']);
        if (array_key_exists('partner_image_url', $data))       $this->setPartnerImageUrl($data['partner_image_url']);
        if (array_key_exists('partner_description', $data))     $this->setPartnerDescription($data['partner_description']);
        if (array_key_exists('partner_keyword', $data))         $this->setPartnerKeyword($data['partner_keyword']);
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
     * @param float $price
     */
    public function setPrice($price) {
        $this->price = (float)$price;
    }

    /**
     * @return float
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

    /**
     * @param int $countLimit
     */
    public function setCountLimit($countLimit) {
        $this->countLimit = $countLimit;
    }

    /**
     * @return int
     */
    public function getCountLimit() {
        return $this->countLimit;
    }

    /**
     * @param string $partner
     */
    public function setPartner($partner) {
        $this->partner = $partner;
    }

    /**
     * @return string
     */
    public function getPartner() {
        return $this->partner;
    }

    /**
     * @param string $partnerDescription
     */
    public function setPartnerDescription($partnerDescription) {
        $this->partnerDescription = $partnerDescription;
    }

    /**
     * @return string
     */
    public function getPartnerDescription() {
        return $this->partnerDescription;
    }

    /**
     * @param string $partnerImageUrl
     */
    public function setPartnerImageUrl($partnerImageUrl) {
        $this->partnerImageUrl = $partnerImageUrl;
    }

    /**
     * @return string
     */
    public function getPartnerImageUrl() {
        return $this->partnerImageUrl;
    }

    /**
     * @param string $partnerKeyword
     */
    public function setPartnerKeyword($partnerKeyword) {
        $this->partnerKeyword = $partnerKeyword;
    }

    /**
     * @return string
     */
    public function getPartnerKeyword() {
        return $this->partnerKeyword;
    }

    /**
     * @param string $partnerUrl
     */
    public function setPartnerUrl($partnerUrl) {
        $this->partnerUrl = $partnerUrl;
    }

    /**
     * @return string
     */
    public function getPartnerUrl() {
        return $this->partnerUrl;
    }

    /**
     * @param string $segmentDescription
     */
    public function setSegmentDescription($segmentDescription) {
        $this->segmentDescription = $segmentDescription;
    }

    /**
     * @return string
     */
    public function getSegmentDescription() {
        return $this->segmentDescription;
    }

    /**
     * @return DiscountCoupon\Entity|null
     */
    public function getDiscount() {
        return $this->discount;
    }

    /**
     * @param DiscountCoupon\Entity|null $discount
     */
    public function setDiscount($discount) {
        $this->discount = $discount;
    }
}