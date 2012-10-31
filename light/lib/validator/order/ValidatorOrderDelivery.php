<?php
namespace light;
  /*
  * This file is part of the symfony package.
  * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

/**
 * sfValidatorString validates a string. It also converts the input value to a string.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorString.class.php 12641 2008-11-04 18:22:00Z fabien $
 */

require_once(dirname(__DIR__."..").'/ValidatorAbstract.php');
require_once(dirname(__DIR__."..").'/ValidatorAnd.php');

class ValidatorOrderDelivery extends ValidatorAbstract
{

  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('max_length', '"%value%" is too long (%max_length% characters max).');
    $this->addMessage('min_length', '"%value%" is too short (%min_length% characters min).');

    $this->addOption('max_length');
    $this->addOption('min_length');

    $this->setOption('empty_value', '');
  }

  /**
   * @see ValidatorAbstract
   */
  protected function doClean($value)
  {
    $clean = (string) $value;

    $length = function_exists('mb_strlen') ? mb_strlen($clean, $this->getCharset()) : strlen($clean);

    if ($this->hasOption('max_length') && $length > $this->getOption('max_length'))
    {
      throw new ValidatorError($this, 'max_length', array('value' => $value, 'max_length' => $this->getOption('max_length')));
    }

    if ($this->hasOption('min_length') && $length < $this->getOption('min_length'))
    {
      throw new ValidatorError($this, 'min_length', array('value' => $value, 'min_length' => $this->getOption('min_length')));
    }

    return $clean;
  }

}
