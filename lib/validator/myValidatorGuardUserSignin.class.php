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
      //$user = $this->getTable()->retrieveByEmailOrPhonenumber($email, $phonenumber);
      $params = array('password' => $password);
      if ($email) {
        $params['email'] = $email;
      }
      else {
        $params['mobile'] = $phonenumber;
      }

      try {
        $result = CoreClient::getInstance()->query('user/auth', $params);
        if (empty($result['token'])) {
          throw new Exception('Не удалось получить токен');
        }
      }
      catch(Exception $e) {
        if ($this->getOption('throw_global_error'))
        {
          throw new sfValidatorError($this, 'invalid');
        }

        throw new sfValidatorErrorSchema($this, array($this->getOption('username_field') => new sfValidatorError($this, 'invalid')));
      }

      $user = RepositoryManager::getUser()->getByToken($result['token']);
      $user->setToken($result['token']);

      return array_merge($values, array('user' => $user));
    }
  }
}
