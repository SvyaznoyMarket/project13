<?php

class ProductSorting
{
  private $position = 0;
  private $list = array();
  private  $active = 'score';

  public function __construct()
  {
    $this->position = 0;
    $this->list = array(
      'price_asc'   => array(
        'name'      => 'price',
        'title'     => 'по цене (сначала дешевые)',
        'direction' => 'asc',
      ),
      'price_desc'   => array(
        'name'      => 'price',
        'title'     => 'по цене (сначала дорогие)',
        'direction' => 'desc',
      ),
      'creator_asc' => array(
        'name'      => 'creator',
        'title'     => 'по производителю (А-Я)',
        'direction' => 'asc',
      ),
      'creator_desc' => array(
        'name'      => 'creator',
        'title'     => 'по производителю (Я-А)',
        'direction' => 'desc',
      ),
      'rating'  => array(
        'name'      => 'rating',
        'title'     => 'по рейтингу',
        'direction' => 'desc',
      ),
      'score'  => array(
        'name'      => 'score',
        'title'     => 'как для своих',
        'direction' => 'desc',
      ),
    );
  }

  public function getList()
  {
    return $this->list;
  }

  public function getActive()
  {
    return $this->list[$this->active];
  }

  public function getDirection()
  {
    return $this->list[$this->active]['direction'];
  }

  public function getCoreSort()
  {
    $active = $this->getActive();
    return array($active['name'] => $active['direction']);
  }

  public function setActive($name, $direction = 'asc')
  {
    $id = $name."_".$direction;
    if(isset($this->list[$id]))
      $this->active = $id;
  }

  public function getDefaults()
  {
    return $this->list;
  }
}