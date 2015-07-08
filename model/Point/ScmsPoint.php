<?php

namespace Model\Point;

use Model\Shop\Subway\Entity as Subway;

class ScmsPoint {

    /** @var int */
    public $uid;
    public $partner;
    public $slug;
    public $workingTime;
    public $latitude;
    public $longitude;
    /** @var string */
    public $name;
    /** @var string */
    public $address;
    /** @var Subway|null */
    public $subway;
    public $icon;

    function __construct(array $data = []) {
        if (isset($data['uid'])) $this->uid = $data['uid'];
        if (isset($data['partner'])) $this->partner = $data['partner'];
        if (isset($data['slug'])) $this->slug = $data['slug'];
        if (isset($data['working_time'])) $this->workingTime = $data['working_time'];

        if (isset($data['location']) && is_array($data['location'])) {
            list($this->longitude, $this->latitude) = $data['location'];
        }

        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['address'])) $this->address = $data['address'];

        if (isset($data['subway'])) {
            if (is_array($data['subway'])) $this->subway = new Subway($data['subway']);
        }

        $iconPath = '/images/deliv-icon/';
        switch ($this->partner) {
            case 'hermes': $this->icon = $iconPath . 'hermes.png'; break;
            case 'svyaznoy': $this->icon = $iconPath . 'svyaznoy.png'; break;
            case 'euroset': $this->icon = $iconPath . 'euroset.png'; break;
            case 'enter': $this->icon = $iconPath . 'enter.png'; break;
            case 'pickpoint':
                $this->icon = $iconPath . (strpos($this->name, 'Постамат') === 0 ? 'pickpoint-postamat.png' : 'pickpoint.png');
                break;
        }
    }

    /**
     * @param $partnersBySlug [] Ассоциативный массив партнеров
     * @return string
     */
    public function getPartnerName($partnersBySlug = []) {
        switch ($this->partner) {
            case 'pickpoint'    : return strpos($this->name, 'Постамат') === false ? 'Пункт выдачи Pickpoint' : 'Постамат Pickpoint';
            default             : return isset($partnersBySlug[$this->partner]) ? @$partnersBySlug[$this->partner]['name'] : '' ;
        }
    }

}