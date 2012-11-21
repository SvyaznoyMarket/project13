<?php

class SubwayRepository
{
    public function create($data)
    {
        $entity = new SubwayEntity($data);

        if (!$entity->getId()) {
            return null;
        }
        return $entity;
    }

    /**
     * @param int $region_id
     * @return SubwayEntity[]
     */
    public function getListByRegionId($region_id)
    {
        if (!(int)$region_id) return array();
        $response = CoreClient::getInstance()->query('subway.get', array(
            'geo_id' => $region_id,
        ));

        $list = array();
        foreach ($response as $data) {
            $list[] = $this->create($data);
        }
        return $list;
    }

}
