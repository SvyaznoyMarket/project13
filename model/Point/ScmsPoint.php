<?php

namespace Model\Point;

use Model\Shop\Subway\Entity as Subway;

class ScmsPoint {

    const PARTNER_SLUG_HERMES = 'hermes';
    const PARTNER_SLUG_PICKPOINT = 'pickpoint';
    const PARTNER_SLUG_EUROSET = 'euroset';
    const PARTNER_SLUG_SVYAZNOY = 'svyaznoy';
    const PARTNER_SLUG_ENTER = 'enter';

    /** @var string */
    public $uid;
    /** @var string */
    public $vendorId;
    /** @var string */
    public $partner;
    /** @var string */
    public $slug;
    /** @var string */
    public $workingTime;
    /** @var float */
    public $latitude;
    /** @var float */
    public $longitude;
    /** @var string */
    public $name;
    /** @var string */
    public $address;
    /** @var Subway|null */
    public $subway;
    /** @var string */
    public $icon;

    function __construct(array $data = []) {
        if (isset($data['uid'])) $this->uid = $data['uid'];
        if (isset($data['vendor_id'])) $this->vendorId = $data['vendor_id'];
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
            case self::PARTNER_SLUG_HERMES: $this->icon = $iconPath . 'hermes.png'; break;
            case self::PARTNER_SLUG_SVYAZNOY: $this->icon = $iconPath . 'svyaznoy.png'; break;
            case self::PARTNER_SLUG_EUROSET: $this->icon = $iconPath . 'euroset.png'; break;
            case self::PARTNER_SLUG_ENTER: $this->icon = $iconPath . 'enter.png'; break;
            case self::PARTNER_SLUG_PICKPOINT:
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
            case self::PARTNER_SLUG_PICKPOINT   : return strpos($this->name, 'Постамат') === false ? 'Пункт выдачи PickPoint' : 'Постамат PickPoint';
            default                             : return isset($partnersBySlug[$this->partner]) ? @$partnersBySlug[$this->partner]['name'] : '' ;
        }
    }

}