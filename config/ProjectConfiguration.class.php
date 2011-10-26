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

    $this->dispatcher->connect('doctrine.configure', array($this, 'listenToConfigureDoctrineEvent'));
  }

  public function listenToConfigureDoctrineEvent(sfEvent $event)
  {
    if (!Doctrine_Core::getLoadedModelFiles())
    {
      self::loadModelFiles();
    }
  }

  protected static function loadModelFiles()
  {
    $dir = sfConfig::get('sf_lib_dir').'/model/doctrine';
    $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
                RecursiveIteratorIterator::LEAVES_ONLY);

    foreach ($it as $file)
    {
      $className = str_replace($dir . DIRECTORY_SEPARATOR, null, $file->getPathName());
      $className = substr($className, 0, strpos($className, '.'));
      Doctrine_Core::loadModel(basename($className), $file->getPathName());
    }
  }
}
