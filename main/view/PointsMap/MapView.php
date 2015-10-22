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
    public $mapConfig = [];

    public function __construct() {
        $region = \App::user()->getRegion();
        $this->mapConfig = [
            'latitude'  => $region->getLatitude(),
            'longitude' => $region->getLongitude(),
            'zoom'      => 10
        ];
    }


    public function getUniquePointCosts(){
        $costs = array_unique(array_map(function(Point $point){ return $point->cost; }, $this->points));
        sort($costs);
        return $costs;
    }

    public function getUniquePointDays(){
        $days = [];
        foreach ($this->points as $point) {
            if (!$point->humanNearestDay) continue;

            $days[$point->humanNearestDay] = $point;
        }


        uasort($days, function(\Model\Point\MapPoint $point1, \Model\Point\MapPoint $point2) {
            if ($point1->dateInterval) {
                $point1DateFrom = strtotime($point1->dateInterval['from']);
                $point1DateTo = strtotime($point1->dateInterval['to']);
            } else {
                $point1DateFrom = $point1DateTo = strtotime($point1->nearestDay);
            }

            if ($point2->dateInterval) {
                $point2DateFrom = strtotime($point2->dateInterval['from']);
                $point2DateTo = strtotime($point2->dateInterval['to']);
            } else {
                $point2DateFrom = $point2DateTo = strtotime($point2->nearestDay);
            }

            if ($point1DateFrom < $point2DateFrom) {
                return -1;
            } else if ($point1DateFrom > $point2DateFrom) {
                return 1;
            } else if ($point1DateFrom == $point2DateFrom) {
                if ($point1DateTo < $point2DateTo) {
                    return -1;
                } else if ($point1DateTo > $point2DateTo) {
                    return 1;
                }
            }

            return 0;
        });
        return array_keys($days);
    }

    public function getUniquePointTokens() {
        $tokens = array_unique(array_map(function(Point $point){ return $point->token; }, $this->points));
        return $tokens;
    }

    public function getDropdownName($token){
        switch ($token) {
            case 'self_partner_pickpoint_pred_supplier':
            case 'self_partner_pickpoint':
                return 'Пункты выдачи PickPoint';
                break;
            case 'self_partner_svyaznoy_pred_supplier':
            case 'self_partner_svyaznoy':
            case 'shops_svyaznoy':
                return 'Магазины Связной';
                break;
            case 'self_partner_hermes_pred_supplier':
            case 'self_partner_hermes':
                return 'Пункты выдачи Hermes';
                break;
            case 'self_partner_euroset_pred_supplier':
            case 'self_partner_euroset':
                return 'Магазины Евросеть';
                break;
            case 'self_partner_formula_m_pred_supplier':
            case 'self_partner_formula_m':
                return 'Пункты выдачи Express4U';
                break;
            default:
                if (strpos($token, MapPoint::POSTAMAT_SUFFIX) !== false) return 'Постаматы PickPoint';
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
                    'token' => $token,
                    'name' => $p->name,
                    'address' => \App::helper()->noBreakSpaceAfterDot($p->address),
                    'subway'    => is_array($p->subway) ? reset($p->subway) : null,

                    /* MapPoint */
                    'regtime' => $p->regtime,
                    'latitude' => $p->latitude,
                    'longitude' => $p->longitude,
                    'marker'    => $orderDelivery->points[$token]->marker,
                    'icon'  => $orderDelivery->points[$token]->icon,
                    'cost'  => (string)$point['cost'],
                    'nearestDay'  => $point['nearestDay'],
                    'dateInterval' => $point['dateInterval'],
                    'blockName'    => $orderDelivery->points[$token]->block_name, // blockName == orderToken ??
                    'orderToken' => $order->block_name,
                    'dropdownName'  => $orderDelivery->points[$token]->dropdown_name,
                    'listName'  => $p->listName,

                ];
                $this->points[] = new Point($data);

                if ($order->delivery && $order->delivery->point && ($order->delivery->point->id === $p->id)) {
                    $order->delivery->point->dateInterval = $point['dateInterval'];
                }
            }
        }

        return $this;
    }

}