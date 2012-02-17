<?php

class BaseRepository
{
  protected $core = null;

  public function __construct()
  {
    $this->core = Core::getInstance();
  }

  public function createQuery($query, array $params = array(), array $data = array())
  {
    return new CoreQuery($query, $params, $data);
  }

  public function get(array $ids)
  {
    return ProductTable::getInstance()->getListByCoreIds($ids, array('hydrate_array' => true));
  }

  public function getOne($id)
  {
    return array_shift($this->get(array($id)));
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
      $q->count();
      $pager->setNbResults($q->count());
    }
  }
}