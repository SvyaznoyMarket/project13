<?php

/**
 * Redis implementation of sfPager for Sorted Sets elements
 * 
 * Example of usage :
 *
 *  $redis_key_name = 'myzset';
 *  $pager = new sfRedisZsetPager($redis_key_name, 10);
 *  // $pager->setParameter('connection', 'connection_name_in_yml'); // optional
 *  // $pager->setParameter('min', '-inf'); // optional
 *  // $pager->setParameter('max', 1980); // optional
 *  $pager->init();
 *
 * The $class parameter of constructor is actually the redis key name
 * 
 * @link      http://code.google.com/p/redis/wiki/SortedSets
 * @version   $Id: sfRedisZsetPager.class.php 30522 2010-08-04 13:13:20Z bicou $
 * @author    Benjamin Viellard <benjamin.viellard@bicou.com>
 */
class sfRedisZsetPager extends sfPager
{
  /**
   * @see   sfPager
   * @link  http://code.google.com/p/redis/wiki/ZrangebyscoreCommand
   * @link  http://code.google.com/p/redis/wiki/ZcardCommand
   */
  public function init()
  {
    $this->resetIterator();

    $client = sfRedis::getClient($this->getParameter('connection', 'default'));

    if ($this->hasParameter('min') or $this->hasParameter('max'))
    {
      // ZCOUNT key min max
      $count = $client->zcount($this->getClass(),
        $this->getParameter('min', '-inf'),
        $this->getParameter('max', '+inf')
      );
    }
    else
    {
      // ZCARD key
      $count = $client->zcard($this->getClass());
    }

    $this->setNbResults($count);

    if (0 == $this->getPage() || 0 == $this->getMaxPerPage() || 0 == $this->getNbResults())
    {
      $this->setLastPage(0);
    }
    else
    {
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
    }
  }

  /**
   * @see   sfPager
   */
  protected function retrieveObject($offset)
  {
    $range = $this->getRange($offset, 1);

    return count($range) ? current($range) : null;
  }

  /**
   * @see   sfPager
   */
  public function getResults()
  {
    return $this->getRange($this->getFirstIndice(), $this->getMaxPerPage());
  }

  /**
   * Returns Redis elements from range
   * 
   * @param integer $offset start index
   * @param integer $count  number of elements to return
   * 
   * @return array
   * @link  http://code.google.com/p/redis/wiki/ZrangebyscoreCommand
   * @link  http://code.google.com/p/redis/wiki/ZrangeCommand
   */
  protected function getRange($offset, $count = 1)
  {
    $client = sfRedis::getClient($this->getParameter('connection', 'default'));
    $start  = $offset - 1;

    if ($this->hasParameter('min') or $this->hasParameter('max'))
    {
      // ZRANGEBYSCORE key min max [LIMIT offset count]
      return $client->zrangebyscore($this->getClass(),
        $this->getParameter('min', '-inf'),
        $this->getParameter('max', '+inf'),
        array('limit' => array('offset' => $start, 'count' => $count))
      );
    }
    else
    {
      // ZRANGE key start end
      return $client->zrange($this->getClass(), $start, $start + $count - 1);
    }
  }
}

