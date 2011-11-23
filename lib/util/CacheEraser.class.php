<?php

class CacheEraser
{

  protected
    $config = null
  ;

  protected static $instance = null;

  /**
   *
   * @return CacheEraser
   */
  static public function getInstance()
  {
    if (null == self::$instance)
    {
      self::$instance = new CacheEraser();
      self::$instance->initialize(sfConfig::get('app_cache_eraser_config'));
    }

    return self::$instance;
  }

  protected function initialize(array $config)
  {
    $this->config = new sfParameterHolder();
    $this->config->add($config);

    $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/cache_eraser.log'));
  }

  public function getConfig($name = null)
  {
    return null == $name ? $this->config->getAll() : $this->config->get($name);
  }

  public function getPrefix()
  {
    return $this->getConfig('prefix');
  }

  public function erase($keys)
  {
    if (empty($keys))
    {
      return false;
    }

    if (!is_array($keys))
    {
      $keys = array($keys);
    }

    $file = $this->getConfig('file');

    $result = file_put_contents($file, implode(";", $keys)."\n", FILE_APPEND | LOCK_EX);
    if (false === $result)
    {
      $this->logger->err('Can\'t write to file');
    }
    else {
      $this->logger->log(sfYaml::dump($keys, 0));
    }
  }

}