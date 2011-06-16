<?php

require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins('sfRediskaPlugin');

    $this->enablePlugins(array(
      'sfDoctrinePlugin',
    ));
  }

  public function configureDoctrine(Doctrine_Manager $manager)
  {
    $manager->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);
    $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CLASS, 'myDoctrineQuery');
    $manager->setAttribute(Doctrine_Core::ATTR_COLLECTION_CLASS, 'myDoctrineCollection');
    //$manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true );

    sfConfig::set('doctrine_model_builder_options', array(
      'baseTableClassName' => 'myDoctrineTable',
      'baseClassName'      => 'myDoctrineRecord',
    ));
  }
}
