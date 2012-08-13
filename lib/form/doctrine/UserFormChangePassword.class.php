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
      
    $this->widgetSchema['password_old'] = new sfWidgetFormInputPassword(array(), array(
      'autocomplete' => 'off',
    ));
    $this->validatorSchema['password_old'] = new sfValidatorString(array('required' => true), array('required' => 'Укажите пароль.'));

    $this->widgetSchema['password_new'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password_new'] = clone $this->validatorSchema['password_old'];
    $this->validatorSchema['password_new']->setOption('required', true);

    $this->validatorSchema['password_old']->setOption('max_length', 18);
    $this->validatorSchema['password_old']->setOption('min_length', 6);
    $this->validatorSchema['password_old']->setMessage('min_length', 'Пароль должен содержать не менее 6 символов.');
    $this->validatorSchema['password_old']->setMessage('max_length', 'Пароль должен содержать не более 18 символов.');

    $this->widgetSchema->setLabels(array(
      'password_old' => 'Старый пароль',
      'password_new' => 'Новый пароль',
    ));
    $this->widgetSchema['password_old']->setAttribute('class', 'text width418 mb15');
    $this->widgetSchema['password_new']->setAttribute('class', 'text width418 mb15');

    $this->widgetSchema->setNameFormat('password[%s]');
    //$this->widgetSchema->setFormFormatterName('default');

    $this->useFields(array(
      'password_old',
      'password_new',
    ));
  }
}
