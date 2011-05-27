<?php

class myDoctrineTable extends Doctrine_Table
{
  public function createBaseQuery(array $params = array())
  {
    $q = $this->createQuery($this->getQueryRootAlias());

    return $q;
  }
  
  public function getQueryRootAlias()
  {
    return lcfirst($this->getComponentName());
  }

  public function createList(array $data = array())
  {
    $return = new Doctrine_Collection($this);
    $return->fromArray($data);
    
    return $return;
  }

  public function createListByIds($ids, $params)
  {
    // TODO: использовать редиска мультигет
    $list = $this->createList();
    foreach ($ids as $id)
    {
      $record = $this->getById($id, $params);
      if ($record)
      {
        $list[] = $record;
      }
    }

    return $list;
  }

  public function getById($id, array $params = array())
  {
    $q = $this->createBaseQuery($params);
    $this->setQueryParameters($q);
    
    $q->where($q->getRootAlias().'.id = ?', $id)
      ->useResultCache(true, null, $this->getRecordHash($id, $params))
    ;
    
    return $q->fetchOne();
  }

  public function getIdBy($column, $value)
  {
    $q = $this->createQuery()
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
      ->where($column.' = ?', $value)
    ;
    
    return $q->fetchOne();
  }
  
  public function getIdsByQuery(Doctrine_Query $q)
  {
    $q = clone $q;
    $q->select('DISTINCT '.$this->getQueryRootAlias().'.id')
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
    ;
    
    $ids = $q->execute();
    if (!is_array($ids))
    {
      $ids = array($ids);
    }
    
    return $ids;
  }

  public function getList(array $params = array())
  {
    $q = $this->createBaseQuery($params);
    $this->setQueryParameters($q, $params);
    
    $ids = $this->getIdsByQuery($q);

    return $this->createListByIds($ids, $params);
  }
  
  public function getForRoute(array $params)
  {
    $q = $this->createBaseQuery()
      ->addWhere($this->getQueryRootAlias().'.id = ?', $params[$this->getQueryRootAlias()])
    ;
    
    return $q->fetchOne();
  }

  public function getChoices($key = 'id', $value = 'name', array $params = array())
  {
    $q = $this->createBaseQuery();
    $params['select'] = $key.','.$value;
    $this->setQueryParameters($q, $params);

    $return = array();
    foreach ($q->execute(array(), Doctrine_Core::HYDRATE_SCALAR) as $item)
    {
      $return[$item[0]] = $item[1];
    }
  }

  public function applyDefaultParameters(array &$params, array $defaults = array())
  {
    $params = myToolkit::arrayDeepMerge($this->getDefaultParameters(), $defaults, $params);
  }
  
  public function getDefaultParameters()
  {
    return array();
  }

  public function setQueryParameters(Doctrine_Query $q, array $params = null)
  {
    // select
    if (isset($params['select']))
    {
      if (is_array($params['select']))
      {
        $select = array();
        foreach ($params['select'] as $column)
        {
          $select[] = $column;
        }
        $params['select'] = implode(',', $select);
      }
      
      if (0 == count($q->getDqlPart('select')))
      {
        $q->select($params['select']);
      }
      else {
        $q->addSelect($params['select']);
      }
    }
    // order by
    if (isset($params['order']))
    {
      if (is_array($params['order']))
      {
        $params['order'] = implode(',', $params['order']);
      }
      if (0 == count($q->getDqlPart('orderby')))
      {
        $q->orderBy($params['order']);
      }
      else {
        $q->addOrderBy($params['order']);
      }
    }
    // offset
    if (isset($params['offset']))
    {
      $q->offset($params['offset']);
    }
    // limit
    if (isset($params['limit']))
    {
      $q->limit($params['limit']);
    }
    // index by
    if (isset($params['index']))
    {
      $q->from("{$this->getClassnameToReturn()} {$q->getRootAlias()} INDEXBY {$q->getRootAlias()}.{$params['index']}");
    }
    // hydrate
    /*
    if (isset($params['hydrate']))
    {
      $q->setHydrationMode(Doctrine_Core::HYDRATE_.strtoupper($params['hydrate']));
    }
    */
  }

  public function getRecordHash($id, array $params = array())
  {
    foreach (array('order', 'limit', 'offset') as $exclude)
    {
      if (isset($params[$exclude]))
      {
        unset($params[$exclude]);
      }
    }

    $paramHash = count($params) > 0 ? md5(serialize(ksort($params))) : '~';
    
    return $this->getQueryRootAlias().'-'.$id.'/'.$paramHash;
  }
}