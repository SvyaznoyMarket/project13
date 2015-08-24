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
    public $humanNearestDay;

//    public $blockName;
    public $orderToken;
    public $dropdownName;
    public $listName;
    /** @var bool Показывать кнопку Купить на карте и в списке точек */
    public $showBuyButton = false;
    public $showBaloonBuyButton = true;
    public $productInShowroom = false;

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

        /* Это уже лишнее но пусть будет пока тут */
        if (isset($data['blockName'])) $this->blockName = $data['blockName'];
        if (isset($data['orderToken'])) $this->orderToken = $data['orderToken'];

        /* Это вообще вынести отсюда как можно быстрее */
        if (isset($data['dropdownName'])) $this->dropdownName = $data['dropdownName'];
        if (isset($data['listName'])) $this->listName = $data['listName'];

        $this->humanNearestDay = $this->humanizeDate();
        $this->humanCost = $this->cost == 0 ? 'Бесплатно' : \App::helper()->formatPrice($this->cost);
        $this->postamatFix();

    }

    public function humanizeDate() {
        return \App::helper()->humanizeDate(\DateTime::createFromFormat('Y-m-d', $this->nearestDay));
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