<?php

class myBaseSorting
{
  protected
    $position = 0,
    $list = array(),
    $active = null
  ;

  public function __construct()
  {
    $this->position = 0;
    $this->list = $this->getDefaults();
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

  public function setQuery(myDoctrineQuery $q)
  {
    call_user_func(array($this, 'setQueryFor'.ucfirst($this->active)), $q);
  }

  public function setActive($name, $direction = 'asc')
  {
    foreach ($this->list as $item)
    {
      if ($name == $item['name'])
      {
        $this->active = $name;
        $this->list[$name]['direction'] = 'asc' == $direction ? 'asc' : 'desc';
        break;
      }
    }
  }

  public function getDefaults()
  {
    return array();
  }
}