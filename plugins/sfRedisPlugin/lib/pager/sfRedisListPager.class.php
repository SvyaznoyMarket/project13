<?php

/**
 * Redis implementation of sfPager for List elements
 * 
 * Example of usage :
 *
 *    $redis_key_name = 'mylist';
 *    $pager = new sfRedisListPager($redis_key_name, 10);
 *    // optional: $pager->setParameter('connection', 'connection_name_in_yml');
 *    $pager->init();
 *
 * The $class parameter of constructor is actually the redis key name
 * 
 * @link      http://code.google.com/p/redis/wiki/Lists
 * @version   $Id: sfRedisListPager.class.php 30522 2010-08-04 13:13:20Z bicou $
 * @author    Benjamin Viellard <benjamin.viellard@bicou.com>
 */
class sfRedisListPager extends sfPager
{
  /**
   * @see   sfPager
   * @link  http://code.google.com/p/redis/wiki/LlenCommand
   */
  public function init()
  {
    $this->resetIterator();

    $client = sfRedis::getClient($this->getParameter('connection', 'default'));

    // LLEN key
    $count  = $client->llen($this->getClass());

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
   * @link  http://code.google.com/p/redis/wiki/LindexCommand
   */
  protected function retrieveObject($offset)
  {
    $client = sfRedis::getClient($this->getParameter('connection', 'default'));

    // LINDEX key index
    return $client->lindex($this->getClass(), $offset - 1); // lists are zero based
  }

  /**
   * @see   sfPager
   * @link  http://code.google.com/p/redis/wiki/LrangeCommand
   */
  public function getResults()
  {
    $client = sfRedis::getClient($this->getParameter('connection', 'default'));
    $start  = $this->getFirstIndice() - 1; // lists are
    $end    = $this->getLastIndice() - 1;  // zero based

    // LRANGE key start end
    return $client->lrange($this->getClass(), $start, $end); // LRANGE end is inclusive
  }

}

