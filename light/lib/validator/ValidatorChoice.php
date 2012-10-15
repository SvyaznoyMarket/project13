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
class ValidatorChoice extends ValidatorAbstract
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * choices:  An array of expected values (required)
   *  * multiple: true if the select tag must allow multiple selections
   *  * min:      The minimum number of values that need to be selected (this option is only active if multiple is true)
   *  * max:      The maximum number of values that need to be selected (this option is only active if multiple is true)
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see ValidatorAbstract
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('choices');
    $this->addOption('multiple', false);
    $this->addOption('min');
    $this->addOption('max');

    $this->addMessage('min', 'At least %min% values must be selected (%count% values selected).');
    $this->addMessage('max', 'At most %max% values must be selected (%count% values selected).');
  }

  /**
   * @see ValidatorAbstract
   */
  protected function doClean($value)
  {
    $choices = $this->getChoices();

    if ($this->getOption('multiple'))
    {
      $value = $this->cleanMultiple($value, $choices);
    }
    else
    {
      if (!self::inChoices($value, $choices))
      {
        throw new ValidatorError($this, 'invalid', array('value' => $value));
      }
    }

    return $value;
  }

  public function getChoices()
  {
    $choices = $this->getOption('choices');
//    if ($choices instanceof sfCallable)
//    {
//      $choices = $choices->call();
//    }

    return $choices;
  }

  /**
   * Cleans a value when multiple is true.
   *
   * @param  mixed $value The submitted value
   * @param  mixed $choices
   *
   * @return array The cleaned value
   *
   * @throws ValidatorError
   */
  protected function cleanMultiple($value, $choices)
  {
    if (!is_array($value))
    {
      $value = array($value);
    }

    foreach ($value as $v)
    {
      if (!self::inChoices($v, $choices))
      {
        throw new ValidatorError($this, 'invalid', array('value' => $v));
      }
    }

    $count = count($value);

    if ($this->hasOption('min') && $count < $this->getOption('min'))
    {
      throw new ValidatorError($this, 'min', array('count' => $count, 'min' => $this->getOption('min')));
    }

    if ($this->hasOption('max') && $count > $this->getOption('max'))
    {
      throw new ValidatorError($this, 'max', array('count' => $count, 'max' => $this->getOption('max')));
    }

    return $value;
  }

  /**
   * Checks if a value is part of given choices (see bug #4212)
   *
   * @param  mixed $value   The value to check
   * @param  array $choices The array of available choices
   *
   * @return Boolean
   */
  static protected function inChoices($value, array $choices = array())
  {
    foreach ($choices as $choice)
    {
      if ((string) $choice == (string) $value)
      {
        return true;
      }
    }

    return false;
  }
}