<?php

class myValidatorMobilePhonenumber extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
  }

  protected function doClean($value)
  {
    $value = preg_replace('/[^\d]/', '', $value);

    // 89031234567 или 79031234567
    if (11 == strlen($value) && in_array(substr($value, 0, 1), array(7, 8)))
    {
      $value = substr($value, 1);
    }

    if (10 != strlen($value))
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

    return $value;
  }
}