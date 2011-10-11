<?php

class myToolkit extends sfToolkit
{
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