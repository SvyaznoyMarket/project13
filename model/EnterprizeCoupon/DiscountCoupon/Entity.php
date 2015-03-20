<?php

namespace Model\EnterprizeCoupon\DiscountCoupon;


class Entity {
    /** @var string */
    private $series;
    /** @var string */
    private $number;
    /** @var string */
    private $promo;
    /** @var string */
    private $amount;
    /** @var string */
    private $title;
    /** @var bool */
    private $used;
    /**
     * @deprecated
     * @var string
     */
    private $from;
    /**
     * @deprecated
     * @var string
     */
    private $to;
    /** @var \DateTime|null */
    private $startDate;
    /** @var \DateTime|null */
    private $endDate;

    public function __construct(array $data = []) {
        if (array_key_exists('series', $data)) $this->setSeries($data['series']);
        if (array_key_exists('number', $data)) $this->setNumber($data['number']);
        if (array_key_exists('promo', $data)) $this->setPromo($data['promo']);
        if (array_key_exists('amount', $data)) $this->setAmount($data['amount']);
        if (array_key_exists('title', $data)) $this->setTitle($data['title']);
        if (array_key_exists('used', $data)) $this->setUsed($data['used']);
        if (array_key_exists('from', $data)) $this->setFrom($data['from']); // deprecated
        if (array_key_exists('to', $data)) $this->setTo($data['to']); // deprecated

        if (array_key_exists('from', $data)) $this->setStartDate($data['from'] ? new \DateTime($data['from']) : null);
        if (array_key_exists('to', $data)) $this->setEndDate($data['to'] ? new \DateTime($data['to']) : null);
    }

    /**
     * @param string $amount
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * @param string $from
     */
    public function setFrom($from) {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getFrom() {
        return $this->from;
    }

    /**
     * @param string $number
     */
    public function setNumber($number) {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * @param string $promo
     */
    public function setPromo($promo) {
        $this->promo = $promo;
    }

    /**
     * @return string
     */
    public function getPromo() {
        return $this->promo;
    }

    /**
     * @param string $series
     */
    public function setSeries($series) {
        $this->series = $series;
    }

    /**
     * @return string
     */
    public function getSeries() {
        return $this->series;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $to
     */
    public function setTo($to) {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getTo() {
        return $this->to;
    }

    /**
     * @param boolean $used
     */
    public function setUsed($used) {
        $this->used = $used;
    }

    /**
     * @return boolean
     */
    public function getUsed() {
        return $this->used;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param \DateTime|null $startDate
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param \DateTime|null $endDate
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }
}