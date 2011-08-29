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

    $this->widgetSchema['email'] = new sfWidgetFormInputText();
    $this->widgetSchema['email']->setLabel('Email');
    $this->validatorSchema['email'] = new sfValidatorEmail(array('max_length' => 100));

    $this->useFields(array(
      'email',
    ));

    $this->widgetSchema->setNameFormat('register[%s]');
    $this->widgetSchema->setFormFormatterName('default');
  }
}
