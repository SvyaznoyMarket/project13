<?php

namespace View\PointsMap;

use \Model\OrderDelivery\Entity\Order as SingleOrder;
use \Model\OrderDelivery\Entity as Order;
use \Model\Point\MapPoint as Point;
use \Model\Product\Delivery\ProductDelivery;
use \Model\Point\MapPoint;

class MapView {

    /** @var Point[] */
    public $points = [];
    /** @var array */
    public $uniqueDays = [];
    /** @var array */
    public $uniqueCosts = [];
    /** @var array */
    public $uniqueTokens = [];
    /** @var array */
    public $mapConfig = [];
    /** @var bool */
    public $isShowroom = false;

    public function __construct() {
        $region = \App::user()->getRegion();
        $this->mapConfig = [
            'latitude'  => $region->getLatitude(),
            'longitude' => $region->getLongitude(),
            'zoom'      => 10
        ];
    }


    public function getUniquePointCosts(){
        $result = [];
        $costs = array_unique(array_map(function(Point $point){ return $point->cost; }, $this->points));
        sort($costs);
        foreach ($costs as $cost) {
            $result[] = [
                'key' => 'cost',
                'value' => $cost,
                'displayValue'  => $cost == 0 ? 'Бесплатно' : \App::helper()->formatPrice($cost)
            ];
        }
        return $result;
    }

    public function getUniquePointDays(){
        $result = [];
        $days = array_unique(array_map(function(Point $point){ return $point->nearestDay; }, $this->points));
        sort($days);
        foreach ($days as $day) {
            $result[] = [
                'key' => 'nearestDay',
                'value' => $day,
                'displayValue'  => \App::helper()->humanizeDate(\DateTime::createFromFormat('Y-m-d', $day))
            ];
        }
        return $result;
    }

    public function getUniquePointTokens() {
        $result = [];
        $tokens = array_unique(array_map(function(Point $point){ return $point->token; }, $this->points));
        foreach ($tokens as $token) {
            $result[] = [
                'key' => 'token',
                'value' => $token,
                'displayValue'  => $this->getDropdownName($token),
                'help'  => \Model\Point\Help::createByPointGroupToken($token),
            ];
        }
        return $result;
    }

    public function getDropdownName($token){
        switch ($token) {
            case 'self_partner_pickpoint_pred_supplier':
            case 'self_partner_pickpoint':
                return 'Пункты выдачи Pickpoint';
                break;
            case 'self_partner_svyaznoy_pred_supplier':
            case 'self_partner_svyaznoy':
            case 'shops_svyaznoy':
                return 'Магазины Связной';
                break;
            case 'self_partner_hermes_pred_supplier':
            case 'self_partner_hermes':
                return 'Пункты выдачи Hermes-DPD';
                break;
            case 'self_partner_euroset_pred_supplier':
            case 'self_partner_euroset':
                return 'Магазины Евросеть';
                break;
            default:
                if (strpos($token, MapPoint::POSTAMAT_SUFFIX) !== false) return 'Постаматы Pickpoint';
                return 'Магазины Enter';
        }
    }

    /** Заполняет список точек информацией из delivery/calc2
     * @param ProductDelivery $delivery
     * @return self
     */
    public function preparePointsWithDelivery(ProductDelivery $delivery) {
        $pointsIds = [];
        foreach ($delivery->getPickup() as $pickup) {
            foreach ($pickup->dates as $deliveryDate) {
                foreach ($deliveryDate->points as $point) {
                    /** @var $point \Model\Shop\Entity */
                    if (array_search($point->getId(), $pointsIds) !== false) continue;
                    $data = [
                        /* BasicPoint */
                        'id' => $point->getId(),
                        'name' => $point->getName(),
                        'address' => \App::helper()->noBreakSpaceAfterDot($point->getAddress()),
                        'subway'    => $point->getSubway()[0],

                        /* MapPoint */
                        'regtime' => '',
                        'latitude' => $point->getLatitude(),
                        'longitude' => $point->getLongitude(),
                        'marker'    => '',
                        'token'  => $pickup->token,
                        'icon'  => '',
                        'cost'  => $pickup->price,
                        'nearestDay'  => $deliveryDate->date->format('Y-m-d'),
                        'nearestDayTimestamp'  => $deliveryDate->date->getTimestamp(),
//                        'blockName'    => $orderDelivery->points[$token]->block_name, // blockName == orderToken ??
//                        'orderToken' => $order->block_name,
                        'dropdownName'  => 'drowdown name',
                        'listName'  => 'list_name',

                    ];
                    $this->points[] = new Point($data);
                    $pointsIds[] = $point->getId();
                }
            }
        }

        return $this;
    }

    /** Заполняет список точек информацией из cart/split
     * @param SingleOrder $order
     * @param Order $orderDelivery
     * @return self
     */
    public function preparePointsWithOrder(SingleOrder $order, Order $orderDelivery) {

        foreach ($order->possible_points as $token => $points) {
            foreach ($points as $point) {
                /** @var $p \Model\OrderDelivery\Entity\Point\DefaultPoint */
                $p = $point['point'];
                $data = [
                    /* BasicPoint */
                    'id' => $p->id,
                    'name' => $p->name,
                    'address' => \App::helper()->noBreakSpaceAfterDot($p->address),
                    'subway'    => is_array($p->subway) ? reset($p->subway) : null,

                    /* MapPoint */
                    'regtime' => $p->regtime,
                    'latitude' => $p->latitude,
                    'longitude' => $p->longitude,
                    'marker'    => $orderDelivery->points[$token]->marker,
                    'token'  => $token,
                    'icon'  => $orderDelivery->points[$token]->icon,
                    'cost'  => (string)$point['cost'],
                    'nearestDay'  => $point['nearestDay'],
                    'nearestDayTimestamp'  => strtotime($point['nearestDay']),
                    'blockName'    => $orderDelivery->points[$token]->block_name, // blockName == orderToken ??
                    'orderToken' => $order->block_name,
                    'dropdownName'  => $orderDelivery->points[$token]->dropdown_name,
                    'listName'  => $p->listName,

                ];
                $this->points[] = new Point($data);
            }
        }

        return $this;
    }

    /** Заполняет список точек информацией из product/get-v3 и scms.enter.ru/shop/get
     * @param \Model\Product\ShopState\Entity[] $shopStates
     * @return self
     */
    public function preparePointsWithShopStates($shopStates) {
        foreach ($shopStates as $shopState) {
            $shop = $shopState->getShop();
            $data = [
                /* BasicPoint */
                'id' => $shop->getId(),
                'name' => $shop->getName(),
                'address' => \App::helper()->noBreakSpaceAfterDot($shop->getAddress()),
                'subway' => isset($shop->getSubway()[0]) ? $shop->getSubway()[0] : null,

                /* MapPoint */
                'regtime' => $shop->getRegime(),
                'latitude' => $shop->getLatitude(),
                'longitude' => $shop->getLongitude(),
                'marker' => [
                    'iconImageSize' => [23, 30],
                    'iconImageOffset' => [-12, -30],
                    'iconImageHref' => '/images/deliv-icon/enter.png',
                ],
                'token' => '',
                'icon' => '/images/deliv-logo/enter.png',
                'cost' => null,
                'nearestDay' => null,
                'nearestDayTimestamp' => null,
                'orderToken' => null,
                'dropdownName' => null,
                'listName' => 'Магазин Enter',

            ];
            $this->points[] = new Point($data);
        }

        return $this;
    }
}