<?php

namespace Model\Region;

class Entity extends BasicRegionEntity {
    /** @var string|null */
    public $kladrId;
    /** @var int */
    public $parentId;
    /** @var bool */
    private $hasTransportCompany;
    /** @var int */
    public $pointCount;
    /** @var \Model\Inflections */
    public $names;

    public function __construct(array $data = []) {
        parent::__construct($data);
        if (isset($data['kladr_id'])) $this->kladrId = substr($data['kladr_id'], 0, 13); // TODO: удалить обрезание, когда будет реализовано https://jira.enter.ru/browse/FCMS-746?focusedCommentId=165705&page=com.atlassian.jira.plugin.system.issuetabpanels:comment-tabpanel#comment-165705
        if (isset($data['parent_id'])) $this->parentId = $data['parent_id'];
        if (isset($data['slug'])) $this->token = $data['slug'];
        if (isset($data['location']['longitude'])) $this->longitude = $data['location']['longitude'];
        if (isset($data['location']['latitude'])) $this->latitude = $data['location']['latitude'];
        if (isset($data['tk_available'])) $this->hasTransportCompany = (bool)$data['tk_available'];
        if (isset($data['number_of_enter_shops']) && isset($data['number_of_pickup_points'])) $this->pointCount = $data['number_of_enter_shops'] + $data['number_of_pickup_points'];
        if (isset($data['names'])) {
            $this->names = new \Model\Inflections($data['names']); // TODO: заработает после реализации FCMS-779
        } else {
            $this->names = new \Model\Inflections();
        }
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getParentId() {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @return boolean
     */
    public function getForceDefaultBuy() {
        if ($this->id == 93751) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function getHasTransportCompany() {
        return $this->hasTransportCompany;
    }
}
