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
    public $ui;
    /** @var string */
    public $id;
    /** @var string */
    public $vendorId;
    /** @var \Model\Point\Partner */
    public $partner;
    /** @var string */
    public $slug;
    /** @var \Model\Point\WorkingTime|null */
    public $workingTime;
    /** @var string */
    public $phone;
    /** @var float */
    public $latitude;
    /** @var float */
    public $longitude;
    /** @var string */
    public $name;
    /** @var string */
    public $url;
    /** @var string */
    public $description;
    /** @var string */
    public $wayWalk;
    /** @var string */
    public $wayAuto;
    /** @var string */
    public $address;
    /** @var Subway|null */
    public $subway;
    /** @var string */
    public $icon;
    /** @var \Model\Point\Town */
    public $town;
    /** @var \Model\Media[] */
    public $medias = [];
    /** @var int */
    public $productCount = 0;

    function __construct($data = []) {
        $this->partner = new \Model\Point\Partner();

        if (isset($data['uid'])) $this->ui = $data['uid'];
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['vendor_id'])) $this->vendorId = $data['vendor_id'];
        if (isset($data['partner'])) $this->partner->slug = $data['partner'];
        if (isset($data['slug'])) $this->slug = $data['slug'];

        // Время работы
        call_user_func(function() use($data) {
            if (isset($data['working_time_array'])) {
                $this->workingTime = new \Model\Point\WorkingTime($data['working_time_array']);
            } else {
                $this->workingTime = new \Model\Point\WorkingTime();
            }

            if (isset($data['working_time'])) {
                $this->workingTime->common = $data['working_time'];
            }

            if (!$this->workingTime->common && !$this->workingTime->days) {
                $this->workingTime = null;
            }
        });

        if (isset($data['phone'])) $this->phone = $data['phone'];

        if (isset($data['location']) && is_array($data['location'])) {
            list($this->longitude, $this->latitude) = $data['location'];
        }

        if (isset($data['name'])) $this->name = $data['name'];

        if (isset($data['description'])) $this->description = $data['description'];
        if (isset($data['address'])) $this->address = $data['address'];
        if (isset($data['way_walk'])) $this->wayWalk = $data['way_walk'];
        if (isset($data['way_auto'])) $this->wayAuto = $data['way_auto'];

        if (isset($data['subway'])) {
            if (is_array($data['subway'])) $this->subway = new Subway($data['subway']);
        }

        $iconPath = '/images/deliv-icon/';
        switch ($this->partner->slug) {
            case self::PARTNER_SLUG_HERMES: $this->icon = $iconPath . 'hermes.png'; break;
            case self::PARTNER_SLUG_SVYAZNOY: $this->icon = $iconPath . 'svyaznoy.png'; break;
            case self::PARTNER_SLUG_EUROSET: $this->icon = $iconPath . 'euroset.png'; break;
            case self::PARTNER_SLUG_ENTER: $this->icon = $iconPath . 'enter.png'; break;
            case self::PARTNER_SLUG_PICKPOINT:
                $this->icon = $iconPath . (strpos($this->name, 'Постамат') === 0 ? 'pickpoint-postamat.png' : 'pickpoint.png');
                break;
        }

        if (isset($data['town'])) {
            $this->town = new \Model\Point\Town($data['town']);
        } else {
            $this->town = new \Model\Point\Town();
        }

        if (isset($data['medias']) && is_array($data['medias'])) {
            foreach ($data['medias'] as $media)
                $this->medias[] = new \Model\Media($media);
        }
        
        $this->url = \App::router()->generate('shop.show', ['pointToken' => $this->slug]);
    }

    /**
     * @param $partnersBySlug [] Ассоциативный массив партнеров
     * @return string
     */
    public function getPartnerName($partnersBySlug = []) {
        switch ($this->partner->slug) {
            case self::PARTNER_SLUG_PICKPOINT   : return strpos($this->name, 'Постамат') === false ? 'Пункт выдачи PickPoint' : 'Постамат PickPoint';
            default                             : return isset($partnersBySlug[$this->partner->slug]) ? @$partnersBySlug[$this->partner->slug]['name'] : '' ;
        }
    }
}