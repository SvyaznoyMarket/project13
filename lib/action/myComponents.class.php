<?php

class myComponents extends sfComponents
{
  public function getLayout()
  {
    return $this->getController()->getActionStack()->getLastEntry()->getActionInstance()->getLayout();
  }

  public function getCacheKey()
  {
    $cache = $this->getCache();

    $params = array();
    foreach ($this->getVarHolder()->getAll() as $k => $v)
    {
      if (($v instanceof myDoctrineRecord) && !empty($v['id']))
      {
        $params[$k] = $v['id'];
      }
      else {
        $params[$k] = $v;
      }
    }
    ksort($params);
    $key = $this->getModuleName().'/'.$this->getActionName().'/'.(count($params) > 0 ? md5(serialize($params)) : '~');

    return $key;
  }

  public function setCachedVars()
  {
    if ($cached = $this->getCache()->get($this->getCacheKey()))
    {
      $this->getVarHolder()->add($cached);

      return true;
    }

    return false;
  }

  public function cacheVars()
  {
    $this->getCache()->set($this->getCacheKey(), $this->getVarHolder()->getAll());
  }

  public function getCache()
  {
    return sfContext::getInstance()->get('cache');
  }
}