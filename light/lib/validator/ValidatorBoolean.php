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
require_once('ValidatorAbstract.php');
class ValidatorBoolean extends ValidatorAbstract
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * true_values:  The list of true values
   *  * false_values: The list of false values
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see ValidatorAbstract
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addOption('true_values', array('true', 't', 'yes', 'y', 'on', '1'));
    $this->addOption('false_values', array('false', 'f', 'no', 'n', 'off', '0'));

    $this->setOption('required', false);
    $this->setOption('empty_value', false);
  }

  /**
   * @see ValidatorAbstract
   */
  protected function doClean($value)
  {
    if (in_array($value, $this->getOption('true_values')))
    {
      return true;
    }

    if (in_array($value, $this->getOption('false_values')))
    {
      return false;
    }

    throw new ValidatorError($this, 'invalid', array('value' => $value));
  }
}