<?php

class myValidatorSClubCardNumber extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
  }

  protected function doClean($value)
  {
    if (strlen(trim($value)) == 0) {
      return '';
    }

    $value = preg_replace('/[^\d]/', '', $value);

    if (!preg_match('/^298[\d]{10}$/i', $value)) //13 цифр, начинается на 298
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

    $replacedString = substr($value, 0, -1); //отсекли последнюю цифру
    $lastNum = intval(substr($value, -1)); //последняя цифра

    $evenNumSumm = 0; //сумма четных
    $oddNumSumm = 0; // сумма нечетных * 3

    for ($i = 11; $i >= 0; $i--) {
      if ($i % 2 == 0) {
        $evenNumSumm += intval($replacedString{$i});
      }
      else {
        $oddNumSumm += (3 * intval($replacedString{$i}));
      }
    }

    $totalSumm = $evenNumSumm + $oddNumSumm;

    $lastNumProceed = 10 - ((($totalSumm % 10) == 0) ? 10 : ($totalSumm % 10));

    if ($lastNum != $lastNumProceed) {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }
    return $value;
  }
}