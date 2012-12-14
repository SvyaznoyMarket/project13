<?php

class RegionRepository
{
  /**
   * @param array $ids
   * @return RegionEntity[]
   */
  public function getListById(array $ids)
  {
    if (empty($ids)) return array();
    $q = new CoreQuery('geo.get', array('id' => $ids));
    $list = array();
    foreach ($q->getResult() as $data) {
      $list[] = $this->create($data);
    }
    return $list;
  }

  /**
   * @return RegionEntity[]
   */
  public function getShopAvailable(){

    $response = CoreClient::getInstance()->query('geo.get-shop-available', array(), array());

    if(!is_array($response) || !isset($response[0])){
      return array();
    }

    $regionList = array();

    foreach($response as $geo){
      $regionList[] = $this->create($geo);
    }

    return $regionList;
  }

    /**
     * @return RegionEntity[]
     */
    public function getShowInMenu(){

        $response = CoreClient::getInstance()->query('geo.get-menu-cities', array(), array());

        if(!is_array($response) || !isset($response[0])){
            return array();
        }

        $regionList = array();

        foreach($response as $geo){
            $regionList[] = $this->create($geo);
        }

        return $regionList;
    }

    /**
   * @param $data
   * @return RegionEntity
   */
  private function create($data)
  {
    $entity = new RegionEntity($data);

    if(!$entity->getId()){
      return null;
    }

    return $entity;
  }

  public function getByToken($token)
  {
    $q = new CoreQuery('geo.get', array('slug' => (string)$token));
    if ($data = reset($q->getResult())) {
      return $this->create($data);
    }
    else return null;
  }

  /**
   * Get default region core_id
   * @return int
   */
  public function getDefaultRegionId()
  {
    return sfContext::getInstance()->getUser()->getRegion('core_id');
  }

  public function getById($id)
  {
    $result = CoreClient::getInstance()->query('geo/get', array('id' => array($id)));

    if ($data = reset($result)) {
      return $this->create($data);
    }
    else return null;
  }

  /**
   * @param string $name
   * @param string $case
   * @return string
   */
  public function getLinguisticCase($name, $case = 'и')
  {
    $cases = array(
      'и' => array(), // именительный
      'р' => array(), // родительный
      'д' => array(), // дательный
      'в' => array(), // винительный
      'т' => array(), // творительный
      'п' => array( // предложный
        'Белгород' => 'Белгороде',
        'Брянск' => 'Брянске',
        'Воронеж' => 'Воронеже',
        'Долгопрудный' => 'Долгопрудном',
        'Климовск' => 'Климовске',
        'Курск' => 'Курске',
        'Липецк' => 'Липецке',
        'Москва' => 'Москве',
        'Ногинск' => 'Ногинске',
        'Орел' => 'Орле',
        'Рязань' => 'Рязани',
        'Санкт-Петербург' => 'Санкт-Петербурге',
        'Серпухов' => 'Серпухове',
        'Смоленск' => 'Смоленске',
        'Сергиев Посад' => 'Сергиевом Посаде',
        'Тамбов' => 'Тамбове',
        'Тверь' => 'Твери',
        'Тула' => 'Туле',
        'Чехов' => 'Чехове',
      ),
    );

    return isset($cases[$case][$name]) ? $cases[$case][$name] : $name;
  }
}