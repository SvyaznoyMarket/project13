<?php

class myValidatorGuardUserRegister extends sfValidatorBase
{

  public function configure($options = array(), $messages = array())
  {
    $this->addOption('username_field', 'username');
    $this->addOption('throw_global_error', false);

    $this->addMessage('unique', 'Пользователь с такими данными уже зарегистрирован.');
    $this->setMessage('invalid', 'Неверный email или номер телефона.');
  }

  protected function doClean($values)
  {
    $username = isset($values[$this->getOption('username_field')]) ? $values[$this->getOption('username_field')] : '';

    $email = '';
    $phonenumber = '';
    foreach (array(
      'email'       => 'myValidatorEmail',
      'phonenumber' => 'myValidatorMobilePhonenumber'
    ) as $field => $class) {
      $validator = new $class(array('required' => true));
      try {
        $$field = $validator->clean($username);
        break;
      }
      catch (Exception $e)
      {
      }
    }

    if (!empty($email) || !empty($phonenumber))
    {
      $user = $this->getTable()->retrieveByEmailOrPhonenumber($email, $phonenumber);
      // user exists?
      if (!$user)
      {
        return array_merge($values, array('email' => $email, 'phonenumber' => $phonenumber));
      }

      if ($this->getOption('throw_global_error'))
      {
        throw new sfValidatorError($this, 'unique');
      }
      throw new sfValidatorErrorSchema($this, array($this->getOption('username_field') => new sfValidatorError($this, 'unique')));
    }

    if ($this->getOption('throw_global_error'))
    {
      throw new sfValidatorError($this, 'invalid');
    }
    throw new sfValidatorErrorSchema($this, array($this->getOption('username_field') => new sfValidatorError($this, 'invalid')));
  }

  protected function getTable()
  {
    return GuardUserTable::getInstance();
  }

}
