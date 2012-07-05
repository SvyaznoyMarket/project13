<?php
namespace light;
use sfYamlParser;
use Exception;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 11.04.12
 * Time: 18:22
 * To change this template use File | Settings | File Templates.
 */
require_once dirname(__FILE__) . '/../../lib/vendor/symfony/lib/yaml/sfYamlParser.php';

class symfonyConfig
{
  private $cached = NULL;
  private $parsed = NULL;

  public static function parseConfig($configPath, $mode){
    try
    {
      if(!is_string($configPath)){
        throw new Exception('configPath must be a string');
      }
      if(!is_string($mode)){
        throw new Exception('config mode must be a string');
      }

      $config = self::getCachedConfig($configPath, $mode);

      if(!is_null($config)){
        $ret = new self();
        $ret->setCached($config);
        return $ret;
      }

      $fileFullPath = realpath(dirname(__FILE__).'/../../').'/config/'.$configPath;

      if(!file_exists($fileFullPath)){
        throw new Exception('config '.$fileFullPath.' not exists');
      }

      $yaml = new sfYamlParser();

      $config = $yaml->parse(file_get_contents($fileFullPath));
//      return $config;
      $ret = new self();
      if(!isset($config[$mode])){
        $mode = 'all';
      }
      $ret->setParsed($config[$mode]);
      return $ret;
    }
    catch(exception $e){
      throw $e;
    }
  }

  public function get($key){
    if(!is_null($this->cached)){
      return isset($this->cached[$key])? $this->cached[$key] : Null;
    }
    if(!is_null($this->parsed)){
      $key = str_replace('app_', '', $key);
      if(array_key_exists($key, $this->parsed)){
        return $this->parsed[$key];
      }
      $keys = explode("_", $key);
      $ret = $this->parsed;
      foreach($keys as $keyName){
        if(isset($ret[$keyName])){
          $ret = $ret[$keyName];
        }
        else{
          return Null;
        }
      }
      return $ret;
    }
  }

  protected static function getCachedConfig($path, $mode){
    $path = str_replace('/', '_', $path);
    $fileFullPath = realpath(dirname(__FILE__).'/../../').'/cache/main/'.$mode.'/config/config_'.$path.'.php';
    if(!file_exists($fileFullPath)){
      return null;
    }

    $config = null;
    $data = file_get_contents($fileFullPath);
    $data = preg_replace('/\<\?php.*?\:\:add\(/ism', '$config =', $data);
    $data = str_replace('));', ');', $data);

    eval($data);
    return $config;
  }

  protected function setCached($data){
    $this->cached = $data;
  }

  protected function setParsed($data){
    $this->parsed = $data;
  }
}