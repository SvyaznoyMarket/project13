<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 21.08.12
 * Time: 13:29
 * To change this template use File | Settings | File Templates.
 */

require_once ('ValidatorError.php');

class ValidatorErrorSchema extends ValidatorError implements \ArrayAccess, \Iterator, \Countable
{
  protected
    $errors       = array(),
    $globalErrors = array(),
    $namedErrors  = array(),
    $count        = 0;

  /**
   * Constructor.
   *
   * @param ValidatorAbstract $validator  An ValidatorAbstract instance
   * @param array           $errors     An array of errors
   */
  public function __construct(ValidatorAbstract $validator, $errors = array())
  {
    $this->validator = $validator;
    $this->arguments = array();

    // override default exception message and code
    $this->code    = '';
    $this->message = '';

    $this->addErrors($errors);
  }

  /**
   * Adds an error.
   *
   * This method merges sfValidatorErrorSchema errors with the current instance.
   *
   * @param ValidatorError $error  An ValidatorError instance
   * @param string           $name   The error name
   *
   * @return ValidatorErrorSchema The current error schema instance
   */
  public function addError(ValidatorError $error, $name = null)
  {
    if (null === $name || is_integer($name))
    {
      if ($error instanceof ValidatorErrorSchema)
      {
        $this->addErrors($error);
      }
      else
      {
        $this->globalErrors[] = $error;
        $this->errors[] = $error;
      }
    }
    else
    {
      if (!isset($this->namedErrors[$name]) && !$error instanceof ValidatorErrorSchema)
      {
        $this->namedErrors[$name] = $error;
        $this->errors[$name] = $error;
      }
      else
      {
        if (!isset($this->namedErrors[$name]))
        {
          $this->namedErrors[$name] = new ValidatorErrorSchema($error->getValidator());
          $this->errors[$name] = new ValidatorErrorSchema($error->getValidator());
        }
        else if (!$this->namedErrors[$name] instanceof ValidatorErrorSchema)
        {
          $current = $this->namedErrors[$name];
          /** @var $current ValidatorError */
          $this->namedErrors[$name] = new ValidatorErrorSchema($current->getValidator());
          $this->errors[$name] = new ValidatorErrorSchema($current->getValidator());

          $method = $current instanceof ValidatorErrorSchema ? 'addErrors' : 'addError';
          $this->namedErrors[$name]->$method($current);
          $this->errors[$name]->$method($current);
        }

        $method = $error instanceof ValidatorErrorSchema ? 'addErrors' : 'addError';
        $this->namedErrors[$name]->$method($error);
        $this->errors[$name]->$method($error);
      }
    }

    $this->updateCode();
    $this->updateMessage();

    return $this;
  }

  /**
   * Adds an array of errors.
   *
   * @param ValidatorErrorSchema|ValidatorError[] $errors  An array of ValidatorError instances
   *
   * @return ValidatorErrorSchema The current error schema instance
   */
  public function addErrors($errors)
  {
    if ($errors instanceof ValidatorErrorSchema)
    {
      /** @var $errors ValidatorErrorSchema */
      foreach ($errors->getGlobalErrors() as $error)
      {
        $this->addError($error);
      }

      foreach ($errors->getNamedErrors() as $name => $error)
      {
        $this->addError($error, (string) $name);
      }
    }
    else
    {
      foreach ($errors as $name => $error)
      {
        $this->addError($error, $name);
      }
    }

    return $this;
  }

  /**
   * Gets an array of all errors
   *
   * @return ValidatorError[] An array of ValidatorError instances
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * Gets an array of all named errors
   *
   * @return ValidatorError[] An array of ValidatorError instances
   */
  public function getNamedErrors()
  {
    return $this->namedErrors;
  }

  /**
   * Gets an array of all global errors
   *
   * @return ValidatorError[] An array of ValidatorError instances
   */
  public function getGlobalErrors()
  {
    return $this->globalErrors;
  }

  /**
   * @see ValidatorError
   */
  public function getValue()
  {
    return null;
  }

  /**
   * @see ValidatorError
   */
  public function getArguments($raw = false)
  {
    return array();
  }

  /**
   * @see ValidatorError
   */
  public function getMessageFormat()
  {
    return '';
  }

  /**
   * Returns the number of errors (implements the Countable interface).
   *
   * @return int The number of array
   */
  public function count()
  {
    return count($this->errors);
  }

  /**
   * Reset the error array to the beginning (implements the Iterator interface).
   */
  public function rewind()
  {
    reset($this->errors);

    $this->count = count($this->errors);
  }

  /**
   * Get the key associated with the current error (implements the Iterator interface).
   *
   * @return string The key
   */
  public function key()
  {
    return key($this->errors);
  }

  /**
   * Returns the current error (implements the Iterator interface).
   *
   * @return mixed The escaped value
   */
  public function current()
  {
    return current($this->errors);
  }

  /**
   * Moves to the next error (implements the Iterator interface).
   */
  public function next()
  {
    next($this->errors);

    --$this->count;
  }

  /**
   * Returns true if the current error is valid (implements the Iterator interface).
   *
   * @return boolean The validity of the current element; true if it is valid
   */
  public function valid()
  {
    return $this->count > 0;
  }

  /**
   * Returns true if the error exists (implements the ArrayAccess interface).
   *
   * @param  string $name  The name of the error
   *
   * @return bool true if the error exists, false otherwise
   */
  public function offsetExists($name)
  {
    return isset($this->errors[$name]);
  }

  /**
   * Returns the error associated with the name (implements the ArrayAccess interface).
   *
   * @param  string $name  The offset of the value to get
   *
   * @return ValidatorError A ValidatorError instance
   */
  public function offsetGet($name)
  {
    return isset($this->errors[$name]) ? $this->errors[$name] : null;
  }

  /**
   * Throws an exception saying that values cannot be set (implements the ArrayAccess interface).
   *
   * @param string $offset  (ignored)
   * @param string $value   (ignored)
   *
   * @throws \LogicException
   */
  public function offsetSet($offset, $value)
  {
    throw new \LogicException('Unable update an error.');
  }

  /**
   * Impossible to call because this is an exception!
   *
   * @param string $offset  (ignored)
   */
  public function offsetUnset($offset)
  {
  }

  /**
   * Updates the exception error code according to the current errors.
   */
  protected function updateCode()
  {
    $this->code = implode(' ', array_merge(
      array_map(create_function('$e', 'return $e->getCode();'), $this->globalErrors),
      array_map(create_function('$n,$e', 'return $n.\' [\'.$e->getCode().\']\';'), array_keys($this->namedErrors), array_values($this->namedErrors))
    ));
  }

  /**
   * Updates the exception error message according to the current errors.
   */
  protected function updateMessage()
  {
    $this->message = implode(' ', array_merge(
      array_map(create_function('$e', 'return $e->getMessage();'), $this->globalErrors),
      array_map(create_function('$n,$e', 'return $n.\' [\'.$e->getMessage().\']\';'), array_keys($this->namedErrors), array_values($this->namedErrors))
    ));
  }

  /**
   * Serializes the current instance.
   *
   * @return string The instance as a serialized string
   */
  public function serialize()
  {
    return serialize(array($this->validator, $this->arguments, $this->code, $this->message, $this->errors, $this->globalErrors, $this->namedErrors));
  }

  /**
   * Unserializes a ValidatorError instance.
   *
   * @param string $serialized  A serialized ValidatorError instance
   *
   * @return void
   */
  public function unserialize($serialized)
  {
    list($this->validator, $this->arguments, $this->code, $this->message, $this->errors, $this->globalErrors, $this->namedErrors) = unserialize($serialized);
  }
}