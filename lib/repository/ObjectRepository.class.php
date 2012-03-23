<?php

abstract class ObjectRepository
{
  /* @var CoreClient */
  protected $coreClient = null;

  public function __construct()
  {
    $this->coreClient = CoreClient::getInstance();
  }

  public function createQuery($query, array $params = array(), array $data = array())
  {
    return new CoreQuery($query, $params, $data);
  }

  abstract public function get(array $ids, $index = null);

  abstract public function create($data);

  public function getAll($index = null)
  {
    return array();
  }

  public function getOne($id)
  {
    return array_shift($this->get(array($id)));
  }

  public function createList($data, $index = null)
  {
    $indexAccessor = $index ? 'get'.ucfirst($index) : null;

    $entities = array();
    foreach ($data as $item)
    {
      $entity = $this->create($item);

      if ($indexAccessor)
      {
        $entities[$entity->$indexAccessor()] = $entity;

      }
      else {
        $entities[] = $entity;
      }

    }

    return $entities;
  }

  protected function applyCriteria(BaseCriteria $criteria, array &$params)
  {
    if ($pager = $criteria->getPager())
    {
      if (null !== $pager->getPage())
      {
        $params['start'] = (string)(($pager->getPage() - 1) * $pager->getMaxPerPage());
        $params['limit'] = (string)$pager->getMaxPerPage();
      }
    }
  }

  protected function applyPager(BaseCriteria $criteria, CoreQuery $q)
  {
    if ($pager = $criteria->getPager())
    {
      $pager->setNbResults($q->count());
    }
  }
}