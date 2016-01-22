<?php
namespace Model\OrderDelivery;

class UserInfoAddressAddition {
    /** @var string */
    public $kladrZipCode = '';
    /** @var string */
    public $kladrStreet = '';
    /** @var string */
    public $kladrStreetType = '';
    /** @var string */
    public $kladrBuilding = '';
    /** @var bool */
    public $isSaveAddressChecked = false;
    /** @var bool */
    public $isSaveAddressDisabled = false;

    /**
     * @param mixed $data
     */
    public function __construct($data = []) {
        if (isset($data['kladrZipCode'])) $this->kladrZipCode = (string)$data['kladrZipCode'];
        if (isset($data['kladrStreet'])) $this->kladrStreet = (string)$data['kladrStreet'];
        if (isset($data['kladrStreetType'])) $this->kladrStreetType = (string)$data['kladrStreetType'];
        if (isset($data['kladrBuilding'])) $this->kladrBuilding = (string)$data['kladrBuilding'];
        if (isset($data['isSaveAddressChecked'])) $this->isSaveAddressChecked = (bool)$data['isSaveAddressChecked'];
        if (isset($data['isSaveAddressDisabled'])) $this->isSaveAddressDisabled = (bool)$data['isSaveAddressDisabled'];
    }
}