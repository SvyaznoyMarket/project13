<?php

// autoload fix
require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

require_once dirname(__FILE__).'/common.php';

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    mb_internal_encoding('UTF-8');

    $this->enablePlugins('sfRediskaPlugin');

    $this->enablePlugins(array(
      'sfDoctrinePlugin',
      //'fpErrorNotifierPlugin',
    ));
  }
}
