<?php

/**
 * UserRegister form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserFormRegister extends BaseForm
{
  public function configure()
  {
    $this->disableCSRFProtection();

    $this->widgetSchema['first_name'] = new sfWidgetFormInputText(array(), array('tabindex' => 5));
    $this->validatorSchema['first_name'] = new sfValidatorString(array('max_length' => 255,  'required' => true));
    $this->widgetSchema['first_name']->setLabel('Имя');

    $this->widgetSchema['username'] = new sfWidgetFormInputText(array(), array('tabindex' => 6));
    $this->validatorSchema['username'] = new sfValidatorOr(array(
      new myValidatorEmail(),
      new myValidatorMobilePhonenumber(),
    ), array(), array('invalid' => 'Неправильный email или номер телефона'));
    $this->widgetSchema['username']->setLabel('Ваш email или мобильный телефон');

    $this->useFields(array(
      'first_name',
      'username',
    ));

    $this->widgetSchema->setNameFormat('register[%s]');
    $this->widgetSchema->setFormFormatterName('default');
  }
}
