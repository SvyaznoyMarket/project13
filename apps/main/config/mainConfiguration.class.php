<?php

class mainConfiguration extends sfApplicationConfiguration
{
  public function configure()
  {
  }

  public function configureDoctrine(Doctrine_Manager $manager)
  {
    //$manager->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);
    $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CLASS, 'myDoctrineQuery');
    $manager->setAttribute(Doctrine_Core::ATTR_COLLECTION_CLASS, 'myDoctrineCollection');
    //$manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true );
    
    sfConfig::set('doctrine_model_builder_options', array(
      'baseTableClassName' => 'myDoctrineTable',
      'baseClassName'      => 'myDoctrineRecord',
    ));
    
    // настройка кеширования
    //$manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, new Doctrine_Cache_Apc());
    //$manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE_LIFESPAN, 60);    
    
    $driver =
      //new Doctrine_Cache_Apc()
      //new Doctrine_Cache_Memcache(array('servers' => array(array('host' => 'localhost', 'port' => 11211, 'persistent' => true)), 'compression' => false))
      //new Doctrine_Cache_Db(array('connection' => $manager->getConnection('cache'), 'tableName' => 'query_cache'))
      //new Doctrine_Cache_Redis(array('server' => 'redis://127.0.0.1:6379', 'prefix' => 'result:'));
      //new Doctrine_Cache_Redis(array('redis' => sfRedis::getClient('localhost'), 'prefix' => 'result:'))
      new Doctrine_Cache_Redis(array('instance' => 'default', 'prefix' => 'dql:'))
    ;
    
    $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $driver);
    $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE_LIFESPAN, 3600);
  }
}
