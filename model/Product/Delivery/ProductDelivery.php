<?php

namespace Model\Product\Delivery;

use Model\Shop\Entity as Shop; // TODO BasicShopEntity
use Model\Region\BasicRegionEntity as Region;
use Point\PickpointPoint;

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
    /** @var bool */
    public $hasPickpointDelivery;
    /** @var bool */
    public $hasEurosetDelivery;
    /** @var bool */
    public $hasHermesDelivery;
    /** @var bool */
    public $hasEnterDelivery;
    /** @var bool */
    public $hasSvyaznoyDelivery;

    public function __construct(array $arr = [], $productId) {

        // Список магазинов
        if (isset($arr['shop_list']) && is_array($arr['shop_list'])) {
            foreach ($arr['shop_list'] as $shopData) {
                $this->shops[$shopData['id']] = new Shop($shopData);

                // SITE-6640
                if (isset($shopData['pickup_point_owner_ui']) && ('0e1c8d17-d7b6-11e0-9274-005056af265b' === $shopData['pickup_point_owner_ui'])) {
                    $this->hasSvyaznoyDelivery = true;
                } else {
                    $this->hasEnterDelivery = true;
                }
            }
        }

        // Список пикпоинтов
        if (isset($arr['pickpoint_list']) && is_array($arr['pickpoint_list'])) {
            foreach ($arr['pickpoint_list'] as $pickpointData) {
                $this->pickpoints[$pickpointData['id']] = new PickpointPoint($pickpointData);
            }
        }

        // Список регионов
        if (isset($arr['geo_list']) && is_array($arr['geo_list'])) {
            foreach ($arr['geo_list'] as $geoData) {
                $this->geo[$geoData['id']] = new Region($geoData);
            }
        }

        // Список интервалов
        if (isset($arr['interval_list']) && is_array($arr['interval_list'])) {
            foreach ($arr['interval_list'] as $intervalData) {
                $this->intervals[$intervalData['id']] = new Interval($intervalData);
            }
        }

        // Список доставок
        if (isset($arr['product_list'][$productId]['delivery_mode_list']) && is_array($arr['product_list'][$productId]['delivery_mode_list'])) {
            foreach ($arr['product_list'][$productId]['delivery_mode_list'] as $deliveryData) {
                $delivery = new Delivery($deliveryData, $this);

                // SITE-6640
                if (false !== strpos($delivery->token, 'pickpoint')) {
                    $this->hasPickpointDelivery = true;
                } else if (false !== strpos($delivery->token, 'euroset')) {
                    $this->hasEurosetDelivery = true;
                } else if (false !== strpos($delivery->token, 'hermes')) {
                    $this->hasHermesDelivery = true;
                }

                $this->deliveries[] = $delivery;
            }
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

        try {
            if ($pickup && !$pickup->dateInterval && \App::abTest()->isOrderWithDeliveryInterval() && ($minDate = $pickup->getMinDate()) && ($dayFrom = $minDate->date->diff((new \DateTime())->setTime(0, 0, 0))->days)) {
                $pickup->dayRange['from'] = ($dayFrom > 1) ? ($dayFrom - 1) : $dayFrom;
                $pickup->dayRange['to'] = $pickup->dayRange['from'] + 2;
            }
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['delivery']);
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

        try {
            if ($delivery && !$delivery->dateInterval && \App::abTest()->isOrderWithDeliveryInterval() && ($minDate = $delivery->getMinDate()) && ($dayFrom = $minDate->date->diff((new \DateTime())->setTime(0, 0, 0))->days)) {
                $delivery->dayRange['from'] = ($dayFrom > 1) ? ($dayFrom - 1) : $dayFrom;
                $delivery->dayRange['to'] = $delivery->dayRange['from'] + 2;
            }
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['delivery']);
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
    /** @var array */
    public $dayRange = [];

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