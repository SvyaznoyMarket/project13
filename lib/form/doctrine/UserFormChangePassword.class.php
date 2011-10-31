<?php

/**
 * UserChangePassword form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserFormChangePassword extends BaseUserForm
{
  public function configure()
  {
    $this->disableCSRFProtection();
      
    $this->useFields(array('password'));

    $this->widgetSchema['password'] = new sfWidgetFormInputPassword(array(), array(
      'autocomplete' => 'off',
    ));
    $this->validatorSchema['password'] = new sfValidatorString(array('max_length' => 18, 'min_length' => 6,  'required' => true), array(
      'required'   => 'Укажите пароль.',
      'min_length' => 'Пароль должен содержать не менее 6 символов.',
      'max_length' => 'Пароль должен содержать не более 18 символов.'
    ));

    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];
    $this->validatorSchema['password_again']->setOption('required', true);

    $this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'Пароли не совпадают.')));

    $this->widgetSchema->setLabels(array(
      'password'       => 'Пароль',
      'password_again' => 'Еще раз пароль',
    ));
    $this->widgetSchema['password']->setAttribute('class', 'text width418 mb15');
    $this->widgetSchema['password_again']->setAttribute('class', 'text width418 mb15');

    $this->widgetSchema->setNameFormat('password[%s]');
    $this->widgetSchema->setFormFormatterName('default');
  }
}
