<?php

namespace Model\Product\Delivery;


use Model\Shop\Entity as Shop; // TODO BasicShopEntity
use Model\Region\BasicRegionEntity as Region;
use Point\PickpointPoint;
use Validator\DateTime;

class ProductDelivery {

    /** @var Interval[] */
    public $intervals = [];
    /** @var Shop[] */
    public $shops = [];
    /** @var */
    public $pickpoints = [];
    /** @var Region[] */
    public $geo = [];
    /** @var Delivery[] */
    public $deliveries = [];

    public function __construct(array $arr = [], $productId) {

        // Список магазинов
        if (isset($arr['shop_list']) && is_array($arr['shop_list'])) {
            foreach ($arr['shop_list'] as $shopData) $this->shops[$shopData['id']] = new Shop($shopData);
        }

        // Список пикпоинтов
        if (isset($arr['pickpoint_list']) && is_array($arr['pickpoint_list'])) {
            foreach ($arr['pickpoint_list'] as $pickpointData) $this->pickpoints[$pickpointData['id']] = new PickpointPoint($pickpointData);
        }

        // Список регионов
        if (isset($arr['geo_list']) && is_array($arr['geo_list'])) {
            foreach ($arr['geo_list'] as $geoData) $this->geo[$geoData['id']] = new Region($geoData);
        }

        // Список интервалов
        if (isset($arr['interval_list']) && is_array($arr['interval_list'])) {
            foreach ($arr['interval_list'] as $intervalData) $this->intervals[$intervalData['id']] = new Interval($intervalData);
        }

        // Список доставок
        if (isset($arr['product_list'][$productId]['delivery_mode_list']) && is_array($arr['product_list'][$productId]['delivery_mode_list'])) {
            foreach ($arr['product_list'][$productId]['delivery_mode_list'] as $deliveryData) $this->deliveries[] = new Delivery($deliveryData, $this);
        }

    }

    /** Возвращает точку по ID ( ненадежно! id могут пересекаться, но пока этого нет )
     * @param $id
     * @return null
     */
    public function getPointById($id) {
        foreach ([$this->shops, $this->pickpoints] as $pointsArr) {
            if (isset($pointsArr[$id])) return $pointsArr[$id];
        }
        return null;
    }

    /** Возвращает список доставок самовывозом
     * @return Delivery[]
     */
    public function getPickup() {
        return array_filter($this->deliveries, function (Delivery $delivery) {
            return $delivery->isPickup;
        });
    }

    /** Возвращает список курьерских доставок
     * @return Delivery[]
     */
    public function getDelivery() {
        return array_filter($this->deliveries, function (Delivery $delivery) {
            return !$delivery->isPickup;
        });
    }

    /** Возвращает самововывоз с минимальным днём
     * @return Delivery|null
     */
    public function getPickupWithMinDate(){
        $pickup = null;
        $minDate = null;
        foreach ($this->getPickup() as $d) {
            foreach ($d->dates as $date) {
                if ($date->date < $minDate || $minDate === null) {
                    $pickup = $d;
                    $minDate = $date->date;
                }
            }
        }
        return $pickup;
    }

    /** Возвращает доставку с минимальным днём
     * @return Delivery|null
     */
    public function getDeliveryWithMinDate(){
        $delivery = null;
        $minDate = null;
        foreach ($this->getDelivery() as $d) {
            foreach ($d->dates as $date) {
                if ($date->date < $minDate || $minDate === null) {
                    $delivery = $d;
                    $minDate = $date->date;
                }
            }
        }
        return $delivery;
    }

}

class Delivery {

    public $id;
    public $token;
    public $name;
    public $price;
    public $isPickup = false;
    /** @var DeliveryDate[] */
    public $dates = [];
    /** @var DateInterval|null */
    public $dateInterval;

    public function __construct(array $arr = [], ProductDelivery &$productDelivery) {
        if (isset($arr['id'])) $this->id = $arr['id'];
        if (isset($arr['token'])) $this->token = $arr['token'];
        if (isset($arr['name'])) $this->name = $arr['name'];
        if (isset($arr['price'])) $this->price = $arr['price'];
        if (isset($arr['is_pickup'])) $this->isPickup = (bool)$arr['is_pickup'];
        if (isset($arr['date_list']) && is_array($arr['date_list'])) {
            foreach ($arr['date_list'] as $dateData) $this->dates[] = new DeliveryDate($dateData, $productDelivery);
        }
        if (isset($arr['date_interval']['from']) && isset($arr['date_interval']['to'])) {
            $this->dateInterval = new DateInterval();
            if ($arr['date_interval']['from']) {
                $this->dateInterval->from = new \DateTime($arr['date_interval']['from']);
            }
            if ($arr['date_interval']['to']) {
                $this->dateInterval->to = new \DateTime($arr['date_interval']['to']);
            }
        }
        // сортируем по дате
        usort($this->dates, function($a, $b){ return $a > $b; });
    }

    /** Минимальная дата для этой доставки
     * @return DeliveryDate
     */
    public function getMinDate() {
        return $this->dates[0];
    }

}

class DeliveryDate {

    /** @var \DateTime */
    public $date;
    /** @var [] */
    public $points = [];

    public function __construct(array $arr = [], ProductDelivery &$productDelivery) {
        if (isset($arr['date'])) $this->date = new \DateTime($arr['date']);
        if (isset($arr['shop_list']) && is_array($arr['shop_list'])) {
            foreach ($arr['shop_list'] as $data) {
                if (isset($data['id']) && $productDelivery->getPointById($data['id'])) $this->points[] = $productDelivery->getPointById($data['id']);
            }
        }
    }

}

class Interval {

    public $start;
    public $end;

    public function __construct(array $arr = []) {
        if (isset($arr['time_begin'])) $this->start = $arr['time_begin'];
        if (isset($arr['time_end'])) $this->end = $arr['time_end'];
    }
}

class DateInterval {
    /** @var \DateTime */
    public $from;
    /** @var \DateTime */
    public $to;
}