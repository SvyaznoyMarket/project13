<?php

namespace Model\Point\WorkingTime;

class Days {
    /** @var \Model\Point\WorkingTime\Days\Day|null */
    public $monday;
    /** @var \Model\Point\WorkingTime\Days\Day|null */
    public $tuesday;
    /** @var \Model\Point\WorkingTime\Days\Day|null */
    public $wednesday;
    /** @var \Model\Point\WorkingTime\Days\Day|null */
    public $thursday;
    /** @var \Model\Point\WorkingTime\Days\Day|null */
    public $friday;
    /** @var \Model\Point\WorkingTime\Days\Day|null */
    public $saturday;
    /** @var \Model\Point\WorkingTime\Days\Day|null */
    public $sunday;


    function __construct($data = []) {
        if (isset($data['monday'])) $this->monday = new \Model\Point\WorkingTime\Days\Day($data['monday']);
        if (isset($data['tuesday'])) $this->tuesday = new \Model\Point\WorkingTime\Days\Day($data['tuesday']);
        if (isset($data['wednesday'])) $this->wednesday = new \Model\Point\WorkingTime\Days\Day($data['wednesday']);
        if (isset($data['thursday'])) $this->thursday = new \Model\Point\WorkingTime\Days\Day($data['thursday']);
        if (isset($data['friday'])) $this->friday = new \Model\Point\WorkingTime\Days\Day($data['friday']);
        if (isset($data['saturday'])) $this->saturday = new \Model\Point\WorkingTime\Days\Day($data['saturday']);
        if (isset($data['sunday'])) $this->sunday = new \Model\Point\WorkingTime\Days\Day($data['sunday']);
    }
}