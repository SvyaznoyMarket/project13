<?php

/**
 * UserSignin form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserFormBasicRegister extends BaseUserForm
{
  public function configure()
  {
    parent::configure();

    $this->widgetSchema['username'] = new sfWidgetFormInputText();
    $this->widgetSchema['username']->setLabel('Email или номер мобильного телефона');
    $this->validatorSchema['username'] = new sfValidatorString(array('max_length' => 128, 'required' => $this->getOption('validate_username', true) ? true : false));

    $this->widgetSchema['first_name'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['first_name'] = new sfValidatorString();

    $this->useFields(array(
      'username',
      'first_name',
    ));

    if ($this->getOption('validate_username', true))
    {
      $this->mergePostValidator(new myValidatorGuardUserRegister());
    }

    $this->widgetSchema->setNameFormat('register[%s]');
    $this->widgetSchema->setFormFormatterName('default');
  }
}
