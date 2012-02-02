<?php

class myValidatorGuardUserSignin extends sfValidatorBase
{

  public function configure($options = array(), $messages = array())
  {
    $this->addOption('username_field', 'username');
    $this->addOption('throw_global_error', false);

    $this->setMessage('invalid', 'Вы неверно указали логин и/или пароль');
  }

  protected function doClean($values)
  {
    $username = isset($values[$this->getOption('username_field')]) ? $values[$this->getOption('username_field')] : '';
    $password = isset($values['password']) ? $values['password'] : '';

    $email = '';
    $phonenumber = '';
    foreach (array(
      'email'       => 'myValidatorEmail',
      'phonenumber' => 'myValidatorMobilePhonenumber'
    ) as $field => $class) {
      $validator = new $class(array('required' => true));
      try {
        $validator->clean($username);
        $$field = $username;
        break;
      }
      catch (Exception $e)
      {
      }
    }
    //myDebug::dump(array($email, $phonenumber), 1);

    // don't allow to sign in with an empty username
    if ($email || $phonenumber)
    {
      $user = $this->getTable()->retrieveByEmailOrPhonenumber($email, $phonenumber);
      // user exists?
      if ($user)
      {
        // password is ok?
        if ($user->getIsActive() && $user->checkPassword($password))
        {
          return array_merge($values, array('user' => $user));
        }
      }
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
