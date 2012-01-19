<?php

class myComponents extends sfComponents
{
  public function getLayout()
  {
    return $this->getController()->getActionStack()->getLastEntry()->getActionInstance()->getLayout();
  }

  public function getCacheKey($params = null)
  {
    $cache = $this->getCache();

    if (null == $params)
    {
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
    }

    $key = $this->getModuleName().'/'.$this->getActionName().'/'.(count($params) > 0 ? md5(serialize($params)) : '~');

    return $key;
  }

  public function setCachedVars($key)
  {
    if ($cached = $this->getCache()->get($key))
    {
      //$this->getVarHolder()->add($cached);
      foreach ($cached as $k => $v)
      {
        $this->setVar($k, $v, true);
      }

      return true;
    }

    return false;
  }

  public function cacheVars($key)
  {
    $cached = array();
    foreach ($this->getVarHolder()->getAll() as $k => $v)
    {
      if ($v instanceof sfOutputEscaperSafe)
      {
        $v = $v->getValue();
      }
      else if ($v instanceof sfOutputEscaper)
      {
        $v = $v->getRawValue();
      }

      $cached[$k] = $v;
    }

    $this->getCache()->set($key, $cached);
  }

  public function getCache()
  {
    return myCache::getInstance();
  }
}