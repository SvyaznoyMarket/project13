<?php

/**
 * UserChangePassword form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserFormChangePassword extends BaseForm
{
  public function configure()
  {
    $this->disableCSRFProtection();
      
    $this->widgetSchema['password'] = new sfWidgetFormInputPassword(array(), array(
      'autocomplete' => 'off',
    ));
    $this->validatorSchema['password'] = new sfValidatorString(array('required' => true), array('required'   => 'Укажите пароль.'));

    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];
    $this->validatorSchema['password_again']->setOption('required', true);

    $this->validatorSchema['password']->setOption('max_length', 18);
    $this->validatorSchema['password']->setOption('min_length', 6);
    $this->validatorSchema['password']->setMessage('min_length', 'Пароль должен содержать не менее 6 символов.');
    $this->validatorSchema['password']->setMessage('max_length', 'Пароль должен содержать не более 18 символов.');

    $this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'Пароли не совпадают.')));

    $this->widgetSchema->setLabels(array(
      'password'       => 'Пароль',
      'password_again' => 'Еще раз пароль',
    ));
    $this->widgetSchema['password']->setAttribute('class', 'text width418 mb15');
    $this->widgetSchema['password_again']->setAttribute('class', 'text width418 mb15');

    $this->widgetSchema->setNameFormat('password[%s]');
    //$this->widgetSchema->setFormFormatterName('default');

    $this->useFields(array(
      'password',
      'password_again',
    ));
  }
}
