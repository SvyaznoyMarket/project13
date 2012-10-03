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

class ValidatorOr extends ValidatorAbstract
{
  protected
    $validators = array();

  /**
   * Constructor.
   *
   * The first argument can be:
   *
   *  * null
   *  * a ValidatorAbstract instance
   *  * an array of ValidatorAbstract instances
   *
   * @param mixed $validators  Initial validators
   * @param array $options     An array of options
   * @param array $messages    An array of error messages
   *
   * @throws \InvalidArgumentException
   *
   * @see ValidatorAbstract
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
      throw new \InvalidArgumentException('ValidatorOr constructor takes a ValidatorAbstract object, or a ValidatorAbstract array.');
    }

    parent::__construct($options, $messages);
  }

  /**
   * @see ValidatorAbstract
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', null);
  }

  /**
   * Adds a validator.
   *
   * @param ValidatorAbstract $validator  An ValidatorAbstract instance
   */
  public function addValidator(ValidatorAbstract $validator)
  {
    $this->validators[] = $validator;
  }

  /**
   * Returns an array of the validators.
   *
   * @return ValidatorAbstract[] An array of ValidatorAbstract instances
   */
  public function getValidators()
  {
    return $this->validators;
  }

  /**
   * @see ValidatorAbstract
   */
  protected function doClean($value)
  {
    $errors = array();
    foreach ($this->validators as $validator)
    {
      try
      {
        /** @var $validator ValidatorAbstract */
        return $validator->clean($value);
      }
      catch (ValidatorError $e)
      {
        $errors[] = $e;
      }
    }

    if ($this->getMessage('invalid'))
    {
      throw new ValidatorError($this, 'invalid', array('value' => $value));
    }

    throw new ValidatorErrorSchema($this, $errors);
  }

  /**
   * @see ValidatorAbstract
   */
  public function asString($indent = 0)
  {
    $validators = '';
    for ($i = 0, $max = count($this->validators); $i < $max; $i++)
    {
      $validators .= "\n".$this->validators[$i]->asString($indent + 2)."\n";

      if ($i < $max - 1)
      {
        $validators .= str_repeat(' ', $indent + 2).'or';
      }

      if ($i == $max - 2)
      {
        $options = $this->getOptionsWithoutDefaults();
        $messages = $this->getMessagesWithoutDefaults();

        if ($options || $messages)
        {
          $validators .= sprintf('(%s%s)',
            $options ? print_r($options, true) : ($messages ? '{}' : ''),
            $messages ? ', '.print_r($messages, true) : ''
          );
        }
      }
    }

    return sprintf("%s(%s%s)", str_repeat(' ', $indent), $validators, str_repeat(' ', $indent));
  }
}