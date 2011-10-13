<?php

/**
 * UserRegister form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserFormRegister extends BaseUserForm
{
  public function configure()
  {
    parent::configure();
    
    $this->disableCSRFProtection();

    $this->widgetSchema['first_name'] = new sfWidgetFormInputText();
    $this->validatorSchema['first_name'] = new sfValidatorString(array('max_length' => 255,  'required' => true));
    $this->widgetSchema['first_name']->setLabel('Имя');

    $this->widgetSchema['last_name'] = new sfWidgetFormInputText();
    $this->validatorSchema['last_name'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $this->widgetSchema['last_name']->setLabel('Фамилия');

    $this->widgetSchema['username'] = new sfWidgetFormInputText();
    $this->validatorSchema['username'] = new sfValidatorString(array('max_length' => 128));
    $this->widgetSchema['username']->setLabel('Ваш email или мобильный телефон');

    $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password']->setOption('required', false);

    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];

    $this->widgetSchema->moveField('password_again', 'after', 'password');

    $this->mergePostValidator(
      //new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'Пароли не одинаковые.'))
      new myValidatorGuardUserRegister()
    );

    $this->useFields(array(
      'first_name',
      //'last_name',
      'username',
      //'password',
      //'password_again',
    ));

    $this->widgetSchema->setNameFormat('register[%s]');
    $this->widgetSchema->setFormFormatterName('default');
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);
  }
}
