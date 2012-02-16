<?php

class BaseRepository
{
  protected $core = null;

  public function __construct()
  {
    $this->core = Core::getInstance();
  }

  public function getCoreResult($query, array $params = array(), array $data = array())
  {
    $response = $this->core->query($query, $params, $data);

    if (isset($response['result']) && ('empty' == $response['result']))
    {
      $response = false;
    }

    return $response;
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

  protected function initPager(BaseCriteria $criteria, $nbResult = 0)
  {
    if ($pager = $criteria->getPager())
    {
      $pager->setNbResult($nbResult);
    }
  }

  public function getOne($id)
  {
    return array_shift($this->get(array($id)));
  }
}