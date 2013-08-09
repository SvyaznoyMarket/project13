<?php

namespace View\Order\NewForm;

class PartField {
    /**
     * Ид типа доставки
     * @var int
     */
    private $deliveryTypeId;
    /**
     * Токен типа доставки (на всякий случай)
     * @var string
     */
    private $deliveryTypeToken;
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
    private $shopId;
    /**
     * Массив ид товаров
     * @var int[]
     */
    private $productIds = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('deliveryType_id', $data)) $this->setDeliveryTypeId($data['deliveryType_id']);
        if (array_key_exists('deliveryType_token', $data)) $this->setDeliveryTypeToken($data['deliveryType_token']);
        if (array_key_exists('date', $data)) {
            try {
                $this->setDate(new \DateTime($data['date']));
            } catch(\Exception $e) {
                $this->setDate(new \DateTime());
                \App::logger()->error(sprintf('Неверная дата %s', json_encode($data['date'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), ['order']);
            }
        }
        if (array_key_exists('interval', $data)) $this->setInterval(new IntervalField((array)$data['interval']));
        if (array_key_exists('shop_id', $data)) $this->setShopId($data['shop_id']);
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
     * @param int $deliveryTypeId
     */
    public function setDeliveryTypeId($deliveryTypeId) {
        $this->deliveryTypeId = (int)$deliveryTypeId;
    }

    /**
     * @return int
     */
    public function getDeliveryTypeId() {
        return $this->deliveryTypeId;
    }

    /**
     * @param string $deliveryTypeToken
     */
    public function setDeliveryTypeToken($deliveryTypeToken) {
        $this->deliveryTypeToken = (string)$deliveryTypeToken;
    }

    /**
     * @return string
     */
    public function getDeliveryTypeToken() {
        return $this->deliveryTypeToken;
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
    public function setShopId($shopId) {
        $this->shopId = (int)$shopId;
    }

    /**
     * @return int
     */
    public function getShopId() {
        return $this->shopId;
    }
}