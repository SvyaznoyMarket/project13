<?php

/**
 * Redis pager on Sorted Sets + pager objects from Doctrine
 *
 * Mix sfPager, Redis and Doctrine to allow paging on Doctrine records with sort in Redis
 *
 * Example of usage:
 *
 *  $redis_key_name = 'user:online';
 *
 *  // somewhere authenticated (negative time for reverse order)
 *  sfRedis::getClient()->zadd($redis_key_name, -time(), $this->getUser()->getGuardUser()->id);
 *
 *  // elsewhere
 *  $pager = new sfRedisZsetDoctrinePager('sfGuardUser', $redis_key_name, 10);
 *  // $pager->setParameter('tableMethod', 'cachedFind'); // optional, defaults to find
 *  // $pager->setParameter('connection', 'connection_name_in_yml'); // optional
 *  // $pager->setParameter('min', '-inf'); // optional
 *  // $pager->setParameter('max', -time() + 3600); // optional
 *  $pager->init();
 *
 *  foreach ($pager as $user)
 *  {
 *    echo link_to($user->username, 'user_profile', $user);
 *  }
 *
 * Notice: looping on the pager trigger as many "find" queries as elements on page,
 * this is far more efficient than doing a WHERE IN + ORDER BY FIELD in the DB,
 * many constant queries by id are also more cacheable
 *
 * @version   $Id: sfRedisZsetDoctrinePager.class.php 30532 2010-08-04 17:59:38Z bicou $
 * @author    Benjamin Viellard <benjamin.viellard@bicou.com>
 * @since     2010-08-04
 */
class sfRedisZsetDoctrinePager extends sfRedisZsetPager
{
  /**
   * create a new pager
   *
   * @param mixed $model      Doctrine model name
   * @param mixed $key        Redis sort set key name
   * @param mixed $maxPerPage Optional, defaults to 10.
   *
   * @author Benjamin Viellard <benjamin.viellard@bicou.com>
   * @since  2010-08-04
   */
  public function __construct($model, $key, $maxPerPage = 10)
  {
    parent::__construct($key, $maxPerPage);
    $this->setParameter('model', $model);
  }

  /**
   * @see   sfRedisZsetPager
   */
  protected function retrieveObject($offset)
  {
    return $this->findObject(parent::retrieveObject($offset));
  }

  /**
   * @see   sfPager
   */
  public function current()
  {
    return $this->findObject(parent::current());
  }

  /**
   * Fetch a Doctrine record from the pager offset value (triggered in loops)
   *
   * @param mixed $member redis zset member, doctrine id
   *
   * @return Doctrine_Record record found from id
   */
  protected function findObject($member)
  {
    if ($member === null) return null;

    $table  = Doctrine_Core::getTable($this->getParameter('model'));
    $method = $this->getParameter('tableMethod', 'find');

    return call_user_func(array($table, $method), $member);
  }
}

