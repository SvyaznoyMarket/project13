<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 21.08.12
 * Time: 13:27
 * To change this template use File | Settings | File Templates.
 */
require_once('ValidatorAbstract.php');

class ValidatorAnd extends ValidatorAbstract
{
  protected
  $validators = array();

  /**
   * Constructor.
   *
   * The first argument can be:
   *
   *  * null
   *  * a sfValidatorBase instance
   *  * an array of sfValidatorBase instances
   *
   * @param mixed $validators Initial validators
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  public function __construct($validators = null, $options = array(), $messages = array())
{
  if ($validators instanceof ValidatorAbstract)
  {
    $this->addValidator($validators);
  }
  else if (is_array($validators))
  {
    foreach ($validators as $validator)
    {
      $this->addValidator($validator);
    }
  }
  else if (null !== $validators)
  {
    throw new \InvalidArgumentException('sfValidatorAnd constructor takes a sfValidatorBase object, or a sfValidatorBase array.');
  }

  parent::__construct($options, $messages);
}

  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * halt_on_error: Whether to halt on the first error or not (false by default)
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
{
  $this->addOption('halt_on_error', false);

  $this->setMessage('invalid', null);
}

  /**
   * Adds a validator.
   *
   * @param ValidatorAbstract $validator  A ValidatorAbstract instance
   */
  public function addValidator(ValidatorAbstract $validator)
{
  $this->validators[] = $validator;
}

  /**
   * Returns an array of the validators.
   *
   * @return array An array of sfValidatorBase instances
   */
  public function getValidators()
{
  return $this->validators;
}

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
{
  $clean = $value;
  $errors = array();
  foreach ($this->validators as $validator)
  {
    try
    {
      $clean = $validator->clean($clean);
    }
    catch (ValidatorError $e)
    {
      $errors[] = $e;

      if ($this->getOption('halt_on_error'))
      {
        break;
      }
    }
  }

  if (count($errors))
  {
    if ($this->getMessage('invalid'))
    {
      throw new ValidatorError($this, 'invalid', array('value' => $value));
    }

    throw new ValidatorErrorSchema($this, $errors);
  }

  return $clean;
}

  /**
   * @see sfValidatorBase
   */
  public function asString($indent = 0)
{
  $validators = '';
  for ($i = 0, $max = count($this->validators); $i < $max; $i++)
  {
    $validators .= "\n".$this->validators[$i]->asString($indent + 2)."\n";

    if ($i < $max - 1)
    {
      $validators .= str_repeat(' ', $indent + 2).'and';
    }

    if ($i == $max - 2)
    {
      $options = $this->getOptionsWithoutDefaults();
      $messages = $this->getMessagesWithoutDefaults();

      if ($options || $messages)
      {
        $validators .= sprintf('(%s%s)',
          $options ? print_r($options, true) : ($messages ? '{}' : ''),
          $messages ? ', '.sprint_r($messages, true) : ''
        );
      }
    }
  }

  return sprintf("%s(%s%s)", str_repeat(' ', $indent), $validators, str_repeat(' ', $indent));
}
}