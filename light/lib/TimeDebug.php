<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 19.04.12
 * Time: 15:02
 * To change this template use File | Settings | File Templates.
 */
class TimeDebug
{

  private static $activeTimes = array();
  private static $historyTimes = array();

  /**
   * @static
   * @param string $name
   */
  public static function start($name){
    if(!isset(self::$activeTimes[$name])){
      self::$activeTimes[$name] = array();
    }
    self::$activeTimes[$name][] = microtime(true);
  }

  /**
   * @static
   * @param string $name
   */
  public static function end($name){
    $end = microtime(true);
    if(isset(self::$activeTimes[$name])){
      foreach(self::$activeTimes[$name] as $time){
        if(!isset(self::$historyTimes[$name])){
          self::$historyTimes[$name] = array();
        }
        self::$historyTimes[$name][] = array(
          'started'  => $time,
          'finished' => $end,
          'delta'    => ($end - $time)
        );
      }
      unset(self::$activeTimes[$name]);
    }
  }

  /**
   * @static
   * @param string $name
   * @return array
   */
  public static function result($name){
    self::end($name);
    if(isset(self::$historyTimes[$name])){
      return self::$historyTimes[$name];
    }
    return array();
  }

  public static function getAll(){
    foreach(self::$activeTimes as $timeName =>$times){
      self::end($timeName);
    }
    return self::$historyTimes;
  }

}
