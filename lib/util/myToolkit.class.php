<?php

class myToolkit extends sfToolkit
{
  static $_months = array(
    1 => 'января',
    2 => 'февраля',
    3 => 'марта',
    4 => 'апреля',
    5 => 'мая',
    6 => 'июня',
    7 => 'июля',
    8 => 'августа',
    9 => 'сентября',
    10 => 'октября',
    11 => 'ноября',
    12 => 'декабря',
  );
  
  static public function declension($int, $expressions)
  {
    if (is_string($expressions)) $expressions = explode(' ', $expressions);
    if (count($expressions) < 3) $expressions[2] = $expressions[1];
    settype($int, "integer");
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
  
  /**
   *
   * @param int $typeId
   * @param int $period
   * @return int|false если false - то не надо показывать этот тип доставки 
   */
  static public function fixDeliveryPeriod($typeId, $period)
  {
    switch ($typeId) {
      case 1:
        if (date('Gi') > 2000) {
          return $period+1;
        }
        break;
      case 2:
        if (date('Gi') > 1415) {
          return false;
        }
        break;
      case 3:
        if (date('Gi') > 2000) {
          return $period+1;
        }
        break;
      default:
        return $period;
        break;
    }
    return $period;
  }

  static public function formatDeliveryDate($period)
  {
    $ts = time() + (3600*24*$period);
    $d = date('j', $ts).' '.self::$_months[date('m', $ts)];
    if ($period == 0) {
      return ' сегодня (' . $d . ')';
    } elseif ($period == 1) {
      return ' завтра (' . $d . ')';
    } elseif ($period == 2) {
      return ' послезавтра (' . $d . ')';
    } else {
      return ' через ' . $period . ' ' . self::declension($period, 'день дня дней');
    }
  }
  
  static public function translite($value)
  {
    $tbl = array(
      'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ж'=>'g', 'з'=>'z',
      'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p',
      'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'ы'=>'i', 'э'=>'e', 'А'=>'A',
      'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ж'=>'G', 'З'=>'Z', 'И'=>'I',
      'Й'=>'Y', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R',
      'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Ы'=>'I', 'Э'=>'E', 'ё'=>'yo', 'х'=>'h',
      'ц'=>'ts', 'ч'=>'ch', 'ш'=>'sh', 'щ'=>'shch', 'ъ'=>'', 'ь'=>'', 'ю'=>'yu', 'я'=>'ya',
      'Ё'=>'YO', 'Х'=>'H', 'Ц'=>'TS', 'Ч'=>'CH', 'Ш'=>'SH', 'Щ'=>'SHCH', 'Ъ'=>'', 'Ь'=>'',
      'Ю'=>'YU', 'Я'=>'YA'
    );

    return strtr($value, $tbl);
  }

  static public function urlize($value)
  {
    return strtolower(preg_replace(array( '/[^-a-zA-Z0-9\s]/', '/[\s]/' ), array('', '-' ), self::translite($value)));
  }

  static public function groupByColumn(array $list, $columnCount)
  {
    $return = array();

    $count = count($list);
    $itemCount_perColumn = intval($count / $columnCount);
    for ($i = 0; $i < $columnCount; $i ++)
    {
      $start = (0 == $i ? 0 : $start + $offset);
      $offset = ($itemCount_perColumn + (($count % $columnCount) >= ($i + 1) ? 1 : 0));

      if ($start > ($count - 1))
      {
        break;
      }
      $return[$i] = array_slice($list, $start, $offset, false);
    }

    return $return;
  }
}