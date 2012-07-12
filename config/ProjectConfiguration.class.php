<?php

define('APP_MAIN_MODULES_PATH', realpath(__DIR__.'/../apps/main/modules/'));

// autoload fix
require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

require_once dirname(__FILE__).'/common.php';
require_once __DIR__.'/../light/lib/log4php/Logger.php';

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    mb_internal_encoding('UTF-8');

    $this->enablePlugins(array(
      'sfDoctrinePlugin',
      //'sfPHPUnit2Plugin',
      //'fpErrorNotifierPlugin',
    ));

    foreach (array(
      array('doctrine.configure', array($this, 'listenToConfigureDoctrineEvent')),
      array('context.load_factories', array($this, 'listenForLoadFactories')),
      //array('debug.web.load_panels', array('myWebDebugPanelEnvironment', 'listenToLoadDebugWebPanelEvent')),
      //array('debug.web.load_panels', array('myWebDebugPanelCore', 'listenToLoadDebugWebPanelEvent')),
      //array('debug.web.load_panels', array('myWebDebugPanelRedis', 'listenToLoadDebugWebPanelEvent')),
    ) as $listener) {
      $this->dispatcher->connect($listener[0], $listener[1]);
    }

    if(!defined('LOG_FILES_PATH'))
    {
        define('LOG_FILES_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR);
    }

    Logger::configure(__DIR__ . '/../light/config/log4php.xml');
  }

  public function listenToConfigureDoctrineEvent(sfEvent $event)
  {
    if (!Doctrine_Core::getLoadedModelFiles())
    {
      $dir = sfConfig::get('sf_lib_dir').'/model/doctrine';
      $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY);

      foreach ($iterator as $file)
      {
        $className = str_replace($dir . DIRECTORY_SEPARATOR, null, $file->getPathName());
        $className = substr($className, 0, strpos($className, '.'));
        Doctrine_Core::loadModel(basename($className), $file->getPathName());
      }
    }
  }

  public function listenForLoadFactories(sfEvent $event)
  {
    //$context = $event->getSubject();
    //$context->set('cache', new myRedisCache(sfConfig::get('app_cache_config')));
  }
}
