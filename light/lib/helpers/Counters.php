<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 18.05.12
 * Time: 12:18
 * To change this template use File | Settings | File Templates.
 */
/**
 * Класс для работы с счетчиками, метриками, акциями и тд
 *
 * Из контроллера наполняется данными, после чего при первом вызове showBlock подгружает шаблоны и
 * наполняет их накопленными данными
 */

class Counters
{
  /**
   * @var array Тут хранятся имена блоков в шаблонах, их привязка к счетчикам
   */
  public static $blocks = array();

  /**
   * @var array шаблоны счетчиков
   */
  public static $counters = array();

  /**
   * @var array используемые в шаблоне переменные
   */
  private static $params = array();

  /**
   * @var bool Свойство указывает, были ли загружены шаблоны Счетчиков (загрузка происходит при первом вызове showBlock)
   */
  private static $areLoadedTemplates = false;

  /**
   * @static
   * @param $blockName
   * @return string | Null
   */
  public static function getBlock($blockName){
    if(!self::$areLoadedTemplates){
      self::$areLoadedTemplates = true;
      require_once(Config::get('viewPath').'template/counters.php');
    }

    if(!array_key_exists($blockName, self::$blocks)){
      return Null;
    }
    $return = '';
    foreach(self::$blocks[$blockName] as $counterName){
      if(array_key_exists($counterName, self::$counters)){
        $return .= self::$counters[$counterName]."\r\n";
      }
    }
    return $return;
  }

  /**
   * @static
   * @param string $name
   * @return string | Null
   */
  public static function getParam($name){
    if(!array_key_exists($name, self::$params)){
      return Null;
    }
    return self::$params[$name];
  }

  /**
   * @static
   * @param string $name
   * @param string $value
   */
  public static function setParam($name, $value){
    if(self::$areLoadedTemplates){
      throw new \RuntimeException('Cant set params after loading seo templates');
    }
    self::$params[$name] = $value;
  }
}