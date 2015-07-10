<?php

namespace Point;


use Model\Point\BasicPoint;

class PickpointPoint extends BasicPoint implements GeoPointInterface {

    public $latitude;
    public $longitude;

    public function __construct(array $data = []) {
        parent::__construct($data);
        if (isset($data['latitude'])) $this->latitude = $data['latitude'];
        if (isset($data['longitude'])) $this->longitude = $data['longitude'];
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }


}