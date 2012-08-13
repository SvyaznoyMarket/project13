<?php

/**
 * User form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserForm extends BaseForm
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    $this->widgetSchema['first_name'] = new sfWidgetFormInputText();
    $this->widgetSchema['first_name']->setLabel('Имя');
    $this->validatorSchema['first_name'] = new sfValidatorString(array('max_length' => 255, 'required' => true));

    $this->widgetSchema['middle_name'] = new sfWidgetFormInputText();
    $this->widgetSchema['middle_name']->setLabel('Отчество');
    $this->validatorSchema['middle_name'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema['last_name'] = new sfWidgetFormInputText();
    $this->widgetSchema['last_name']->setLabel('Фамилия');
    $this->validatorSchema['last_name'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema['gender'] = new sfWidgetFormChoice(array('choices' => array(1 => 'мужской', 2 => 'женский')));
    $this->widgetSchema['gender']->setLabel('Пол');
    $this->validatorSchema['gender'] = new sfValidatorChoice(array('choices' => array(1, 2), 'required' => false));

    $this->widgetSchema['email'] = new sfWidgetFormInputText();
    $this->widgetSchema['email']->setLabel('E-mail');
    $this->validatorSchema['email'] = new sfValidatorString(array('max_length' => 128, 'required' => false));

    $this->widgetSchema['phonenumber'] = new sfWidgetFormInputText();
    $this->widgetSchema['phonenumber']->setLabel('Мобильный телефон');
    $this->validatorSchema['phonenumber'] = new sfValidatorString(array('max_length' => 128, 'required' => false));

    $this->widgetSchema['phonenumber_city'] = new sfWidgetFormInputText();
    $this->widgetSchema['phonenumber_city']->setLabel('Домашний телефон');
    $this->validatorSchema['phonenumber_city'] = new sfValidatorString(array('max_length' => 128, 'required' => false));

    $this->widgetSchema['skype'] = new sfWidgetFormInputText();
    $this->widgetSchema['skype']->setLabel('Skype');
    $this->validatorSchema['skype'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema['birthday'] = new sfWidgetFormDate();
    $this->widgetSchema['birthday']->setLabel('Дата рождения');
    $this->validatorSchema['birthday'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema['occupation'] = new sfWidgetFormInputText();
    $this->widgetSchema['occupation']->setLabel('Род деятельности');
    $this->validatorSchema['occupation'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $useFields = array(
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
	  $this->widgetSchema['gender']->setOption('choices', array(1 => 'Мужской', 2 => 'Женский'));
    $this->widgetSchema['gender']->setAttribute('class', 'styled');
    $this->validatorSchema['gender'] = new sfValidatorChoice(array('choices' => array(1, 2), 'required' => false));

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
