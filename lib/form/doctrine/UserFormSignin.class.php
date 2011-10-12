<?php

/**
 * UserSignin form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserFormSignin extends BaseUserForm
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    $this->setWidgets(array(
      'username' => new sfWidgetFormInputText(array(), array('tabindex' => 1)),
      'password' => new sfWidgetFormInputPassword(array('type' => 'password'), array('tabindex' => 2)),
      'remember' => new sfWidgetFormInputCheckbox(), // array(), array('tabindex' => 3)
    ));

    $this->setValidators(array(
      'username' => new sfValidatorString(),
      'password' => new sfValidatorString(),
      'remember' => new sfValidatorBoolean(),
    ));

    $this->validatorSchema->setPostValidator(new myValidatorGuardUserSignin());

    $this->widgetSchema->setLabels(array(
      'username' => 'Логин',
      'password' => 'Пароль',
      'remember' => 'Забыли пароль?',
    ));
    
    $this->useFields(array(
      'username',
      'password',
      'remember',
    ));

    $this->widgetSchema->setNameFormat('signin[%s]');
    $this->widgetSchema->setFormFormatterName('default');
  }
}
