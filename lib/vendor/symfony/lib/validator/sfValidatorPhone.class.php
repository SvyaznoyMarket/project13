<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorPhone validates phone.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorPhone.class.php 22149 2009-09-18 14:09:53Z Kris.Wallsmith $
 */
class sfValidatorPhone extends sfValidatorRegex
{
  const REGEX_PHONE = '/^[0-9\-\(\)\s]{5,25}$/';

  /**
   * @see sfValidatorRegex
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setOption('pattern', self::REGEX_PHONE);
  }

  
}