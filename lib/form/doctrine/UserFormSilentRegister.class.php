<?php

/**
 * UserSignin form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserFormSilentRegister extends BaseUserForm
{
  public function configure()
  {
    parent::configure();

    $this->widgetSchema['username'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['username'] = new myValidatorMobilePhonenumber(array('required' => true));

    $this->widgetSchema['first_name'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['first_name'] = new sfValidatorString(array('required' => true));

    $this->useFields(array(
      'username',
      'first_name',
    ));

    $this->mergePostValidator(new myValidatorGuardUserRegister());

    $this->widgetSchema->setNameFormat('register[%s]');
    $this->widgetSchema->setFormFormatterName('default');
  }
}
