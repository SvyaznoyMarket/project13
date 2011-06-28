<?php

class myDoctrineCacheRedis extends Doctrine_Cache_Redis
{

  public function deleteByPattern($pattern)
  {
    $keys = $this->_rediska->getKeysByPattern($pattern);
    foreach ($keys as $key)
    {
      $this->_rediska->delete($key);
    }

    return count($keys);;
  }

}