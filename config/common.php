<?php

/**
 * @param $value
 * @return string
 */
function formatPrice($value){
    return number_format($value, 0, ',', ' ');
}


if (!function_exists('mb_ucfirst'))
{
  function mb_ucfirst($str, $encoding = 'UTF-8', $lower_str_end = false)
  {
    $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
    $str_end = '';
    if ($lower_str_end)
    {
      $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
    }
    else
    {
      $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
    $str = $first_letter.$str_end;

    return $str;
  }
}

if (!function_exists('mb_lcfirst'))
{
  function mb_lcfirst($str, $encoding = 'UTF-8', $lower_str_end = false)
  {
    $first_letter = mb_strtolower(mb_substr($str, 0, 1, $encoding), $encoding);
    $str_end = '';
    if ($lower_str_end)
    {
      $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
    }
    else
    {
      $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
    $str = $first_letter.$str_end;

    return $str;
  }
}