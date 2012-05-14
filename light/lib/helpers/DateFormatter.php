<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 17.04.12
 * Time: 15:10
 * To change this template use File | Settings | File Templates.
 */
class DateFormatter
{

  /**
   * @static
   * @param string $date //Format Y-m-d or Y-m-d H:i:s
   * @return string
   */
  public static function Humanize($date){
    $today = new DateTime();
    $today->settime(0,0,0);
    $date = new DateTime($date);

    $interval = $today->diff($date);
    if($interval->d == 0){
      return 'сегодня ('.$date->format('d.m.Y').')';
    }
    if($interval->d == 1 && $interval->invert == 0){ //если invert = 1 - значит дата уже прошла
      return 'завтра ('.$date->format('d.m.Y').')';
    }
    if($interval->d == 2 && $interval->invert == 0){ //если invert = 1 - значит дата уже прошла
      return 'послезавтра ('.$date->format('d.m.Y').')';
    }

    return 'через '.$interval->d.' '.self::declination($interval->d , 'день дня дней');
//    return $date->format('d.m.Y');
  }

  static public function declination($int, $expressions)
  {
    if (is_string($expressions)) $expressions = explode(' ', $expressions);
    if (count($expressions) < 3) $expressions[2] = $expressions[1];
    $int = (int) $int;
    $count = $int % 100;
    if ($count >= 5 && $count <= 20) {
      $result = $expressions[2];
    } else {
      $count = $count % 10;
      if ($count == 1) {
        $result = $expressions[0];
      } elseif ($count >= 2 && $count <= 4) {
        $result = $expressions[1];
      } else {
        $result = $expressions[2];
      }
    }
    return $result;
  }
}