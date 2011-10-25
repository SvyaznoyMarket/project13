<?php

/**
 * UserSignin form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserFormQuickRegister extends BaseUserForm
{
  public function configure()
  {
    parent::configure();

    $this->widgetSchema['username'] = new sfWidgetFormInputText();
    $this->widgetSchema['username']->setLabel('Логин');
    $this->validatorSchema['username'] = new sfValidatorEmail(array('max_length' => 100));

    $this->useFields(array(
      'username',
    ));

    $this->mergePostValidator(
      //new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'Пароли не одинаковые.'))
      new myValidatorGuardUserRegister()
    );

    $this->widgetSchema->setNameFormat('register[%s]');
    $this->widgetSchema->setFormFormatterName('default');
  }
}
