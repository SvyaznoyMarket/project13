<?php

class ShopRepository
{
  public function create($data)
  {
    $entity = new ShopEntity($data);

    if (!$entity->getId()) {
        return null;
    }
    return $entity;
  }

  public function count() {
    $result = Core::getInstance()->query('shop/get', array(
      'expand' => array(),
      'count'  => 'true',
    ));

    return isset($result['count']) ? $result['count'] : 0;
  }

    /**
     * @param array $ids
     * @return ShopEntity[]
     */
    public function getListById(array $ids)
    {
        if (empty($ids)) return array();
        $response = CoreClient::getInstance()->query('shop.get', array(
            'id' => $ids,
        ));

        $list = array();
        foreach ($response as $data) {
            $list[] = $this->create($data);
        }
        return $list;
    }
}