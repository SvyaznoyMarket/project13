<?php

class CacheEraser
{

  protected
    $config = null;

  protected static $instance = null;

  /**
   *
   * @return CacheEraser
   */
  static public function getInstance()
  {
    if (null == self::$instance) {
      self::$instance = new CacheEraser();
      self::$instance->initialize(sfConfig::get('app_cache_eraser_config'));
    }

    return self::$instance;
  }

  protected function initialize(array $config)
  {
    $this->config = new sfParameterHolder();
    $this->config->add($config);

    $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cache_eraser.log'));
  }

  public function getConfig($name = null)
  {
    return null == $name ? $this->config->getAll() : $this->config->get($name);
  }

  public function getPrefix()
  {
    return $this->getConfig('prefix');
  }

  public function erase($keys, $is_only_log = false, $extra = null)
  {
    if (empty($keys)) {
      return false;
    }

    if (!is_array($keys)) {
      $keys = array($keys);
    }

    $this->log($keys, $extra);

    if ($is_only_log) {
      return true;
    }

    $file = $this->getConfig('file');

    foreach ($keys as & $key) {
      if (substr($key, -1, 1) != "-") {
        $key .= ';';
      }
    }
    $result = file_put_contents($file, implode("\n", $keys) . "\n", FILE_APPEND | LOCK_EX);
    if (false === $result) {
      $this->logger->err('Can\'t write to file');
    }
    else {
      $this->logger->log(sfYaml::dump($keys, 0));
    }
  }

  public function log($keys, $extra = null)
  {
    if (empty($keys)) {
      return false;
    }

    if (!is_array($keys)) {
      $keys = array($keys);
    }

    foreach ($keys as $key)
    {
      $record = new CacheEraserLog();
      list($data['type'], $data['entity_id'], $data['region_id']) = explode('-', $key);

      $record->type = $data['type'];

      switch ($data['type'])
      {
        case 'product':
          $entity_id = ProductTable::getInstance()->getRecordByCoreId('product', $data['entity_id'], true);
          break;
        case 'productCategory':
          $entity_id = ProductCategoryTable::getInstance()->getRecordByCoreId('productCategory', $data['entity_id'], true);
          break;
        default:
          $entity_id = null;
      }
      $record->entity_id = $entity_id;

      $record->region_id = !empty($data['region_id']) ? RegionTable::getInstance()->findOneByGeoipCode($data['region_id'])->id : null;

      $record->extra = !empty($extra) ? $extra : null;

      $record->save();

      unset($record);
    }
  }

}