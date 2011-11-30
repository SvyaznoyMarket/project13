<?php

class myRedisCache extends sfRediskaCache
{
	public function addTag($tag, $key)
	{
    return $this->_rediska->addToSet($tag, $key);
	}

	public function removeTag($tag, $key)
	{
    return $this->_rediska->deleteFromSet($tag, $key);
	}

	public function getByTag($tag)
	{
    if (func_num_args() > 1)
    {
      $tag = func_get_args();
    }

    $keys =
      is_array($tag)
      ? $this->_rediska->unionSets($tag)
      : $this->_rediska->getSet($tag)
    ;

    return count($keys) ? $this->getMany($keys) : null;
	}

	public function hasTag($tag)
	{
    return $this->_rediska->existsInSet($tag);
	}

	public function removeByTag($tag)
	{
    if (func_num_args() > 1)
    {
      $tag = func_get_args();
    }

    $keys = array();
    foreach(
      is_array($tag)
      ? $this->_rediska->unionSets($tag)
      : $this->_rediska->getSet($tag)
    as $key) {
      $keys[] = $this->getKey($key);
      $keys[] = $this->getKey($key, 'lastmodified');
    }

    return $this->_rediska->delete($keys);
	}
}