<?php

namespace Model\Point;

use Model\Shop\Subway\Entity as Subway;

class ScmsPoint {

    const PARTNER_SLUG_FORMULA_M = 'formula-m';
    const PARTNER_SLUG_HERMES = 'hermes';
    const PARTNER_SLUG_PICKPOINT = 'pickpoint';
    const PARTNER_SLUG_EUROSET = 'euroset';
    const PARTNER_SLUG_SVYAZNOY = 'svyaznoy';
    const PARTNER_SLUG_ENTER = 'enter';

    const ICON_PATH = '/images/deliv-icon/';

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
    public $descriptionHtml;
    /** @var string */
    public $wayWalkHtml;
    /** @var string */
    public $wayAutoHtml;
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

    public function __construct($data = []) {
        $this->partner = new \Model\Point\Partner();

        if (isset($data['uid'])) $this->ui = $data['uid'];
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['vendor_id'])) $this->vendorId = $data['vendor_id'];
        if (isset($data['partner'])) $this->partner->slug = $data['partner'];
        if (isset($data['slug'])) $this->slug = $data['slug'];

        // Время работы
        call_user_func(function() use($data) {
            if (!empty($data['working_time']) || (!empty($data['working_time_array']) && is_array($data['working_time_array']))) {
                $this->workingTime = new \Model\Point\WorkingTime();

                if (!empty($data['working_time'])) {
                    $this->workingTime->string = (string)$data['working_time'];
                }

                if (!empty($data['working_time_array']) && is_array($data['working_time_array'])) {
                    if (isset($data['working_time_array']['common'])) {
                        // TODO удалить данный блок после релиза FCMS-851

                        $days = [
                            'monday' => 'Понедельник',
                            'tuesday' => 'Вторник',
                            'wednesday' => 'Среда',
                            'thursday' => 'Четверг',
                            'friday' => 'Пятница',
                            'saturday' => 'Суббота',
                            'sunday' => 'Воскресенье',
                        ];

                        foreach ($days as $en => $ru) {
                            if (!empty($data['working_time_array'][$en][0]) && !empty($data['working_time_array'][$en][1])) {
                                $this->workingTime->array[] = $ru . ' ' . $data['working_time_array'][$en][0] . '—' . $data['working_time_array'][$en][1];
                            }
                        }
                    } else {
                        $this->workingTime->array = $data['working_time_array'];
                    }
                }
            }
        });

        if (isset($data['phone'])) $this->phone = $data['phone'];

        if (isset($data['location']) && is_array($data['location'])) {
            list($this->longitude, $this->latitude) = $data['location'];
        }

        if (isset($data['name'])) $this->name = $data['name'];

        if (isset($data['description'])) $this->descriptionHtml = $data['description'];
        if (isset($data['address'])) $this->address = $data['address'];
        if (isset($data['way_walk'])) $this->wayWalkHtml = $data['way_walk'];
        if (isset($data['way_auto'])) $this->wayAutoHtml = $data['way_auto'];

        if (isset($data['subway'])) {
            if (is_array($data['subway'])) $this->subway = new Subway($data['subway']);
        }

        switch ($this->partner->slug) {
            case self::PARTNER_SLUG_FORMULA_M: $this->icon = self::ICON_PATH . 'formula_m.png'; break;
            case self::PARTNER_SLUG_HERMES: $this->icon = self::ICON_PATH . 'hermes.png'; break;
            case self::PARTNER_SLUG_SVYAZNOY: $this->icon = self::ICON_PATH . 'svyaznoy.png'; break;
            case self::PARTNER_SLUG_EUROSET: $this->icon = self::ICON_PATH . 'euroset.png'; break;
            case self::PARTNER_SLUG_ENTER: $this->icon = self::ICON_PATH . 'enter.png'; break;
            case self::PARTNER_SLUG_PICKPOINT:
                $this->icon = self::ICON_PATH . (strpos($this->name, 'Постамат') === 0 ? 'pickpoint-postamat.png' : 'pickpoint.png');
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
        
        $this->url = \App::router()->generateUrl('shop.show', ['pointToken' => $this->slug]);
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