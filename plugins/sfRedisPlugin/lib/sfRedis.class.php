<?php

/**
 * sfRedis
 *
 * @package   sfRedisPlugin
 * @author    Benjamin VIELLARD <bicou@bicou.com>
 * @license   The MIT License
 * @version   SVN: $Id: sfRedis.class.php 28813 2010-03-26 19:47:10Z bicou $
 */
class sfRedis
{
  /**
   * Array of configuration parameters
   * @var array
   */
  private static $config = null;

  /**
   * Array of Predis clients
   * @var array
   */
  private static $clients = array();

  /**
   * Initialize
   *
   * @param array $config
   * @static
   * @access public
   * @return void
   */
  public static function initialize($config)
  {
    self::$config = $config;
  }

  /**
   * Create a predis client on demand
   *
   * @param string $connection
   * @static
   * @access public
   * @return Predis_Client
   */
  public static function getClient($connection = 'default')
  {
    if (!isset(self::$clients[$connection]))
    {
      $parameters = isset(self::$config['connections'][$connection]) ? self::$config['connections'][$connection] : null;
      self::$clients[$connection] = Predis_Client::create($parameters);
    }

    return self::$clients[$connection];
  }
}

