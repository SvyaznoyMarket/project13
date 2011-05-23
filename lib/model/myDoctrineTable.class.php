<?php

class myDoctrineTable extends Doctrine_Table
{
  public function createBaseQuery(array $params = array())
  {
    return $this->createQuery($this->getQueryRootAlias());
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
    $list = $this->createList();
    foreach ($ids as $id)
    {
      $list[] = $this->getById($id, $params);
    }

    return $list;
  }
  
  public function getIdsByQuery(Doctrine_Query $q)
  {
    $q = clone $q;
    $q->select($this->getQueryRootAlias().'.id');
    $q->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    
    return $q->execute();
  }

  public function getList(array $params = array())
  {
    $q = $this->createBaseQuery($params);
    $this->setQueryParameters($q, $params);
    
    $ids = $this->getIdsByQuery($q);

    return $this->createListByIds($ids, $params);
  }

  public function getById($id, array $params = array())
  {
    $q = $this->createBaseQuery($params);
    $this->setQueryParameters($q);
    
    $q->where($q->getRootAlias().'.id = ?', $id);
    
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
    myToolkit::arrayDeepMerge($defaults, $params);
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
      if (0 == count($q->getDqlPart('order')))
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
}