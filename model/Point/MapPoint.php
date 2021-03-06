<?php

namespace Model\Point;


class MapPoint extends BasicPoint {

    const POSTAMAT_SUFFIX = '_postamat';

    /** @var string Токен точки (shops, pickpoint) */
    public $token;
    /** @var string */
    public $regtime;
    /** @var float */
    public $latitude;
    /** @var float */
    public $longitude;
    /** @var array */
    public $marker;
    /** @var string */
    public $icon;
    /** @var float|int */
    public $cost;
    /** @var string */
    public $humanCost;
    /** @var string */
    public $nearestDay;
    /** @var string */
    public $dateInterval;
    /** @var string */
    public $humanNearestDay;
    /** @var bool */
    public $fitsAllProducts = false;
    /** @var bool Является ли точка пикпоинтом */
    public $isPickpoint;

//    public $blockName;
    public $orderToken;
    public $dropdownName;
    public $listName;
    /** @var bool Показывать кнопку Купить на карте и в списке точек */
    public $showBuyButton = false;
    public $showBaloonBuyButton = true;
    public $productInShowroom = false;
    /** @var array */
    public $dayRange = [];

    public function __construct(array $data = []) {
        parent::__construct($data);
        if (isset($data['token'])) $this->token = $data['token'];
        if (isset($data['regtime'])) $this->regtime = $data['regtime'];
        if (isset($data['latitude'])) $this->latitude = $data['latitude'];
        if (isset($data['longitude'])) $this->longitude = $data['longitude'];
        if (isset($data['marker'])) $this->marker = $data['marker'];
        if (isset($data['icon'])) $this->icon = $data['icon'];
        if (isset($data['cost'])) $this->cost = $data['cost'];
        if (isset($data['nearestDay'])) $this->nearestDay = $data['nearestDay'];
        if (isset($data['dateInterval'])) $this->dateInterval = $data['dateInterval'];
        if (isset($data['fitsAllProducts'])) $this->fitsAllProducts = $data['fitsAllProducts'];

        try {
            if (!$this->dateInterval && \App::abTest()->isOrderWithDeliveryInterval()) {
                $date = new \DateTime($this->nearestDay);
                if ($dayFrom = $date->diff((new \DateTime())->setTime(0, 0, 0))->days) {
                    $this->dayRange['from'] = ($dayFrom > 1) ? ($dayFrom - 1): $dayFrom;
                    $this->dayRange['to'] = $this->dayRange['from'] + 2;
                }
            }
        } catch (\Exception $e) {}

        /* Это уже лишнее но пусть будет пока тут */
        if (isset($data['blockName'])) $this->blockName = $data['blockName'];
        if (isset($data['orderToken'])) $this->orderToken = $data['orderToken'];

        /* Это вообще вынести отсюда как можно быстрее */
        if (isset($data['dropdownName'])) $this->dropdownName = $data['dropdownName'];
        if (isset($data['listName'])) $this->listName = $data['listName'];

        if (!\App::abTest()->isHiddenDeliveryInterval()) {
            $this->humanNearestDay = $this->humanizeDate();
        }
        $this->humanCost = $this->cost == 0 ? 'Бесплатно' : \App::helper()->formatPrice($this->cost);
        $this->isPickpoint = strpos($this->token, 'pickpoint') !== false;
        $this->postamatFix();

    }

    /**
     * @return string|null
     */
    public function humanizeDate() {
        $return = null;

        if ($this->dateInterval) {
            $from = !empty($this->dateInterval['from']) ? \DateTime::createFromFormat('Y-m-d', $this->dateInterval['from']) : null;
            $to = !empty($this->dateInterval['to']) ? \DateTime::createFromFormat('Y-m-d', $this->dateInterval['to']) : null;

            $return = sprintf(
                '%s %s',
                $from ? ('с ' . $from->format('d.m')) : '',
                $to ? (' по ' . $to->format('d.m')) : ''
            );
        } else if ($this->dayRange) {
            $return = sprintf('%s-%s %s', $this->dayRange['from'], $this->dayRange['to'], \App::helper()->numberChoice($this->dayRange['to'], ['день', 'дня', 'дней']));
        } else if ($this->nearestDay) {
            $return = \App::helper()->humanizeDate(\DateTime::createFromFormat('Y-m-d', $this->nearestDay));
        }

        return $return;
    }

    /**
     * Фикс для разделения точек Pickpoint на постаматы и ПВЗ
     */
    private function postamatFix() {
        if (strpos($this->name, 'Постамат') === 0 && strpos($this->token, 'pickpoint') !== false) {
            $this->marker['iconImageHref'] = '/images/deliv-icon/pickpoint-postamat.png';
            $this->icon = '/images/deliv-logo/pickpoint-postamat.png';
            $this->dropdownName = 'Постаматы PickPoint';
            $this->token .= self::POSTAMAT_SUFFIX;
        }
    }

}