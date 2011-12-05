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
    $return = new myDoctrineCollection($this);

    if (!empty($data))
    {
      $return->fromArray($data);
    }

    return $return;
  }

  public function createListByIds($ids, array $params = array())
  {
    $this->applyDefaultParameters($params, array('index' => false));

    $alias = $this->getQueryRootAlias();

    foreach (array('offset', 'limit') as $k)
    {
      if (isset($params[$k]))
      {
        unset($params[$k]);
      }
    }

    // TODO: использовать редиска мультигет
    $list = $this->createList();
    foreach ($ids as $id)
    {
      $record = $this->getById($id, $params);

      if ($record)
      {
        $index = ($params['index'] && isset($params['index'][$alias]) && $this->hasColumn($params['index'][$alias]))
          ? $params['index'][$alias]
          : false
        ;

        if ($index)
        {
          $list[$record[$index]] = $record;
        }
        else
        {
          $list[] = $record;
        }
      }
    }

    return $list;
  }

  public function getById($id, array $params = array())
  {
    if (!$id)
    {
      return false;
    }

    // если гидрация массивом, тогда использовать кеш
    if (isset($params['hydrate_array']) && $params['hydrate_array'])
    {
      $cache = $this->getCache();

      $key = $this->getRecordQueryHash($id, $params);
      if ($cached = $cache->get($key))
      {
        return $cached;
      }

      $record = $this->getRecordById($id, $params);
      if ($record)
      {
        $cache->set($key, $record);
        foreach ($this->getCacheTags($record) as $tag)
        {
          $cache->addTag($tag, $key);
        }
      }
    }
    else {
      $record = $this->getRecordById($id, $params);
    }

    return $record;
  }

  public function getCacheTags($record)
  {
    $alias = $this->getQueryRootAlias();

    $tags = array(
      $alias,
    );
    if (!empty($record['id']))
    {
      $tags[] = "{$alias}-{$record['id']}";
    }

    return $tags;
  }

  public function getRecordById($id, array $params = array())
  {
    $q = $this->createBaseQuery($params);
    $this->setQueryParameters($q);

    $q->where($q->getRootAlias().'.id = ?', $id);

    return $q->fetchOne();
  }

  public function getIdBy($column, $value)
  {
    $q = $this->createQuery()
      ->select('id')
      ->where($column.' = ?', $value)
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
    ;

    return $q->fetchOne();
  }

  public function getByToken($token, array $params = array())
  {
    $id = $this->getIdBy('token', $token);

    return $this->getById($id);
  }

  public function getIdsByQuery(Doctrine_Query $query, array $params = array(), $hash = false, $tags = array())
  {
    $q = clone $query;
    $q->select('DISTINCT '.$this->getQueryRootAlias().'.id')
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
    ;

    if (!empty($hash))
    {
      // cache
      $cache = $this->getCache();

      $key = $this->getQueryHash($hash, $params);
      if ($cached = $cache->get($key))
      {
        return $cached;
      }
    }

    $ids = $q->execute();
    if (!is_array($ids))
    {
      $ids = array($ids);
    }

    if (!empty($hash))
    {
      if (!is_array($tags))
      {
        $tags = array($tags);
      }

      $cache->set($key, $ids);
      foreach ($tags as $tag)
      {
        $cache->addTag($tag, $key);
      }
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

    return $q->execute()->toKeyValueArray($key, $value);
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
    if (isset($params['order']) && ('_index' != $params['order']))
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
    // group by
    if (isset($params['group']))
    {
      $q->groupBy($params['group']);
    }
    // index by
    /*
    if (isset($params['index']))
    {
      $q->from("{$this->getClassnameToReturn()} {$q->getRootAlias()} INDEXBY {$q->getRootAlias()}.{$params['index']}");
    }
    */
    // hydrate
    /*
    if (isset($params['hydrate']))
    {
      $q->setHydrationMode(Doctrine_Core::HYDRATE_.strtoupper($params['hydrate']));
    }
    */
  }

  public function getRecordQueryHash($id, array $params = array())
  {
    foreach (array('order', 'limit', 'offset') as $exclude)
    {
      if (isset($params[$exclude]))
      {
        unset($params[$exclude]);
      }
    }

    return $this->getQueryHash($this->getQueryRootAlias().'-'.$id, $params);
  }

  public function getQueryHash($path, array $params = array())
  {
    ksort($params);
    $paramHash = count($params) > 0 ? md5(serialize($params)) : '~';

    return $path.'/'.$paramHash;
  }

  public function getCacheKeys(myDoctrineRecord $record)
  {
    $keys = array(
      '*'.$this->getQueryRootAlias().'-all/*',
      '*'.$this->getQueryRootAlias().'-count/*',
    );

    if ($this->hasField('id'))
    {
      $keys[] = '*'.$this->getQueryRootAlias().'-'.$record->id.'/*';
      $keys[] = '*'.$this->getQueryRootAlias().'-ids/*';
    }

    return $keys;
  }

  public function getCacheEraserKeys($record, $action = null)
  {
    return array();

    if (in_array($this->getComponentName(), array(
      'Task',
      'sfCombine',
    ))) {
      return array();
    }

    $field = false;
    if ($record instanceof myDoctrineRecord)
    {
      foreach (array('core_id', 'id') as $v)
      {
        if ($this->hasColumn($v))
        {
          $field = $v;
          break;
        }
      }
    }

    if (!$field)
    {
      return array();
    }

    return array(
      lcfirst($this->getComponentName()).'-'.$record[$field],
    );
  }

  public function createRecordFromCore(array $data)
  {
    $record = $this->create();
    $record->importFromCore($data);

    return $record;
  }

  public function getCoreMapping()
  {
    return null;
  }

  public static function getRecordByCoreId($model, $coreId, $returnId = false)
  {
    if (empty($coreId))
    {
      return false;
    }

    return $returnId
      ? Doctrine_Core::getTable($model)->createQuery()
          ->select('id')
          ->where('core_id = ?', $coreId)
          ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
          ->fetchOne()
      : Doctrine_Core::getTable($model)->findOneByCoreId($coreId)
    ;
  }

  public function getCache()
  {
    return sfContext::getInstance()->get('cache');
  }
}