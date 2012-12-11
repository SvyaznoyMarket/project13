<?php

namespace Model\Region;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $parentId;
    /** @var string */
    private $name;
    /** @var string */
    private $token;
    /** @var bool */
    private $isMain;
    /** @var bool */
    private $hasShop;
    /** @var bool */
    private $hasDelivery;
    /** @var bool */
    private $hasSubway;
    /** @var bool */
    private $hasService;
    /** @var float */
    private $latitude;
    /** @var float */
    private $longitude;
    /** @var Entity */
    private $parent;
    /** @var bool */
    private $hasTransportCompany;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('parent_id', $data)) $this->setParentId($data['parent_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('is_main', $data)) $this->setIsMain($data['is_main']);
        if (array_key_exists('has_shop', $data)) $this->setHasShop($data['has_shop']);
        if (array_key_exists('has_delivery', $data)) $this->setHasDelivery($data['has_delivery']);
        if (array_key_exists('has_subway', $data)) $this->setHasSubway($data['has_subway']);
        if (array_key_exists('has_f1', $data)) $this->setHasService($data['has_f1']);
        if (array_key_exists('coord_long', $data)) $this->setLongitude($data['coord_long']);
        if (array_key_exists('coord_lat', $data)) $this->setLatitude($data['coord_lat']);
        if (array_key_exists('tk_available', $data)) $this->setHasTransportCompany($data['tk_available']);
    }

    /**
     * @param boolean $hasDelivery
     */
    public function setHasDelivery($hasDelivery)
    {
        $this->hasDelivery = $hasDelivery;
    }

    /**
     * @return boolean
     */
    public function getHasDelivery()
    {
        return $this->hasDelivery;
    }

    /**
     * @param boolean $hasService
     */
    public function setHasService($hasService)
    {
        $this->hasService = $hasService;
    }

    /**
     * @return boolean
     */
    public function getHasService()
    {
        return $this->hasService;
    }

    /**
     * @param boolean $hasShop
     */
    public function setHasShop($hasShop)
    {
        $this->hasShop = $hasShop;
    }

    /**
     * @return boolean
     */
    public function getHasShop()
    {
        return $this->hasShop;
    }

    /**
     * @param boolean $hasSubway
     */
    public function setHasSubway($hasSubway) {
        $this->hasSubway = (bool)$hasSubway;
    }

    /**
     * @return boolean
     */
    public function getHasSubway() {
        return $this->hasSubway;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param boolean $isMain
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;
    }

    /**
     * @return boolean
     */
    public function getIsMain()
    {
        return $this->isMain;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Entity|null $parent
     */
    public function setParent(Entity $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return Entity|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param int $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param bool $hasTransportCompany
     */
    public function setHasTransportCompany($hasTransportCompany) {
        $this->hasTransportCompany = (bool)$hasTransportCompany;
    }

    /**
     * @return bool
     */
    public function getHasTransportCompany() {
        return $this->hasTransportCompany;
    }

    public function getInflectedName($inflect = 5) {
        if (!$this->id) {
            return $this->name;
        }
        if ($inflect < 0 || $inflect > 5) {
            throw new \InvalidArgumentException(sprintf('Неправильный индекс склонения "%s"', $inflect));
        }

        // cache
        if ((5 == $inflect) ) {
            switch ($this->id) {
                case 14974:
                    return 'Москве';
                    break;
                case 108136:
                    return 'Санкт-Петербурге';
                    break;
                case 13242:
                    return 'Орле';
                    break;
                case 18074:
                    return 'Воронеже';
                    break;
                case 83210:
                    return 'Брянске';
                    break;
            }
        }

        try {
            $data = file_get_contents(\App::config()->dataDir . '/inflect/region/' . $this->id . '.json');
            $data = json_decode($data);

            return array_key_exists($inflect, $data) ? $data[$inflect] : $this->name;
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        return $this->name;
    }
}
