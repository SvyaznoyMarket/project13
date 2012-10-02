<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 21.08.12
 * Time: 13:27
 * To change this template use File | Settings | File Templates.
 */
require_once('ValidatorRegex.php');

class ValidatorEmail extends ValidatorRegex
{
  const REGEX_EMAIL = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,3})$/i';

  /**
   * @see ValidatorRegex
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setOption('pattern', self::REGEX_EMAIL);
  }
}