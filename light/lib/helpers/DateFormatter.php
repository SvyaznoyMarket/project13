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

  public static function CoreFullDateToOrderForm($date){
    $today = new DateTime();
    $today->settime(0,0,0);
    $date = new DateTime($date);

    $interval = $today->diff($date);
    if($interval->d == 1 && $interval->invert == 0){ //если invert = 1 - значит дата уже прошла
      return 'завтра ('.$date->format('d.m.Y').')';
    }
    if($interval->d == 0){
      return 'сегодня ('.$date->format('d.m.Y').')';
    }
    return $date->format('d.m.Y');
  }
}