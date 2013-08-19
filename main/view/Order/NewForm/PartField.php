<?php

namespace View\Order\NewForm;

class PartField {
    /** @var string */
    private $deliveryMethod_token;
    /**
     * Дата получения заказа, например, 2013-08-23
     * @var \DateTime
     */
    private $date;
    /**
     * Интервал получения заказа, например, ['18:00', '21:00']
     * @var IntervalField
     */
    private $interval;
    /**
     * Ид магазина
     * @var int
     */
    private $pointId;
    /**
     * Массив ид товаров
     * @var int[]
     */
    private $productIds = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('deliveryMethod_token', $data)) $this->setDeliveryMethodToken($data['deliveryMethod_token']);
        if (array_key_exists('date', $data)) {
            $date = date('Y-m-d', $data['date'] / 1000);
            try {
                $this->setDate(new \DateTime($date));
            } catch(\Exception $e) {
                $this->setDate(new \DateTime());
                \App::logger()->error(['action' => __METHOD__, 'date' => $data], ['order']);
            }
        }
        if (array_key_exists('interval', $data)) $this->setInterval(new IntervalField((array)$data['interval']));
        if (array_key_exists('point_id', $data)) $this->setPointId($data['point_id']);
        if (array_key_exists('products', $data)) $this->setProductIds((array)$data['products']);
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date) {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * @param string $deliveryMethod_token
     */
    public function setDeliveryMethodToken($deliveryMethod_token) {
        $this->deliveryMethod_token = (string)$deliveryMethod_token;
    }

    /**
     * @return string
     */
    public function getDeliveryMethodToken() {
        return $this->deliveryMethod_token;
    }

    /**
     * @param \View\Order\NewForm\IntervalField $interval
     */
    public function setInterval(IntervalField $interval) {
        $this->interval = $interval;
    }

    /**
     * @return \View\Order\NewForm\IntervalField
     */
    public function getInterval() {
        return $this->interval;
    }

    /**
     * @param \int[] $productIds
     */
    public function setProductIds(array $productIds) {
        $this->productIds = $productIds;
    }

    /**
     * @return \int[]
     */
    public function getProductIds() {
        return $this->productIds;
    }

    /**
     * @param int $shopId
     */
    public function setPointId($shopId) {
        $this->pointId = (int)$shopId;
    }

    /**
     * @return int
     */
    public function getPointId() {
        return $this->pointId;
    }
}