<?php

/**
 * User form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserForm extends BaseUserForm
{
  /**
   * @see GuardUserForm
   */
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

  //  $this->widgetSchema['id']->setLabel('ID');
    //$this->validatorSchema['id'] = new sfValidatorPass();

    $this->widgetSchema['first_name']->setLabel('Имя');

    $this->widgetSchema['middle_name']->setLabel('Отчество');
    $this->validatorSchema['middle_name'] = new sfValidatorPass();

    $this->widgetSchema['last_name']->setLabel('Фамилия');
    $this->validatorSchema['last_name'] = new sfValidatorPass();

    $this->widgetSchema['gender']->setLabel('Ваш пол');

    $this->widgetSchema['email']->setLabel('E-mail');

    $this->widgetSchema['phonenumber']->setLabel('Мобильный телефон');

    $this->widgetSchema['phonenumber_city']->setLabel('Домашний телефон');

    $this->widgetSchema['skype']->setLabel('Skype');

    $this->widgetSchema['birthday']->setLabel('Дата рождения');

    $this->widgetSchema['occupation']->setLabel('Род деятельности');
    $this->validatorSchema['occupation'] = new sfValidatorPass();

    #print_r(get_class_methods($this->widgetSchema['occupation']));
    $useFields = array(
//      'id',
      'first_name',
      'middle_name',
      'last_name',
      'gender',
      'email',
      'phonenumber',
      'phonenumber_city',
      'skype',
      'birthday',
      'occupation',
    );
    //одинаковые стили для всех полей
    foreach($useFields as $field){
        $this->widgetSchema[$field]->setAttribute('class', 'text width418 mb10');
    }
    //кроме
	$this->widgetSchema['gender']->setOption('choices', array('male' => 'Мужской', 'female' => 'Женский'));
    $this->widgetSchema['gender']->setAttribute('class', 'styled');

	$years = range(date('Y') - 7, date('Y') - 80);
    $this->widgetSchema['birthday']->setOption('years', array_combine($years, $years));
    $this->widgetSchema['birthday']->setAttribute('class', 'styled');

    $this->useFields($useFields);

    $this->widgetSchema->setNameFormat('user[%s]');
  }

  protected function updateNameColumn($value)
  {
    if (empty($value))
    {
      $value = $this->getValue('user');
    }

    return $value;
  }

}
