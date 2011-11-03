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
  
  public function setup()
  {
      
    parent::setup();
      
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'email'            => new sfWidgetFormInputText(),
      'phonenumber'      => new sfWidgetFormInputText(),
      'first_name'       => new sfWidgetFormInputText(),
      'last_name'        => new sfWidgetFormInputText(),
      'middle_name'      => new sfWidgetFormInputText(),
      'nickname'         => new sfWidgetFormInputText(),
      'algorithm'        => new sfWidgetFormInputText(),
      'salt'             => new sfWidgetFormInputText(),
      'password'         => new sfWidgetFormInputText(),
      'is_active'        => new sfWidgetFormInputCheckbox(),
      'is_super_admin'   => new sfWidgetFormInputCheckbox(),
      'last_login'       => new sfWidgetFormDateTime(),
      'last_ip'          => new sfWidgetFormInputText(),
      'region_id'        => new sfWidgetFormInputText(),
      'is_legal'         => new sfWidgetFormInputCheckbox(),
      'type'             => new sfWidgetFormChoice(array('choices' => array('admin' => 'admin', 'client' => 'client', 'partner' => 'partner'))),
      'gender'           => new sfWidgetFormChoice(array('choices' => array('male' => 'male', 'female' => 'female'))),
      'birthday'         => new sfWidgetFormDate(),
      'photo'            => new sfWidgetFormInputText(),
      'phonenumber_city' => new sfWidgetFormInputText(),
      'skype'            => new sfWidgetFormInputText(),
      'address'          => new sfWidgetFormTextarea(),
      'occupation'       => new sfWidgetFormInputText(),
      'settings'         => new sfWidgetFormTextarea(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
      'core_id'          => new sfWidgetFormInputText(),
      'group_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'GuardGroup')),
      'permission_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'GuardPermission')),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'email'            => new myValidatorEmail(array('max_length' => 128, 'required' => false, 'trim'=>true),array('invalid'=>'Вы указали неверный email.')),
      'phonenumber'      => new myValidatorMobilePhonenumber(array('required' => false, 'trim'=>true),array('invalid'=>"Вы неверно указали номер телефона.")),
      'first_name'       => new sfValidatorString(array('max_length' => 255, 'trim'=>true)),
      'last_name'        => new sfValidatorString(array('max_length' => 255, 'trim'=>true)),
      'middle_name'      => new sfValidatorString(array('max_length' => 255, 'trim'=>true,'required'=>true)),
      'nickname'         => new sfValidatorString(array('max_length' => 128, 'required' => false, 'trim'=>true)),
      'algorithm'        => new sfValidatorString(array('max_length' => 128, 'required' => false, 'trim'=>true)),
      'salt'             => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'password'         => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'is_active'        => new sfValidatorBoolean(array('required' => false)),
      'is_super_admin'   => new sfValidatorBoolean(array('required' => false)),
      'last_login'       => new sfValidatorDateTime(array('required' => false)),
      'last_ip'          => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'region_id'        => new sfValidatorInteger(array('required' => false)),
      'is_legal'         => new sfValidatorBoolean(array('required' => false)),
      'type'             => new sfValidatorChoice(array('choices' => array(0 => 'admin', 1 => 'client', 2 => 'partner'), 'required' => false)),
      'gender'           => new sfValidatorChoice(array('choices' => array(0 => 'male', 1 => 'female'), 'required' => false)),
      'birthday'         => new sfValidatorDate(array('required' => false),array('invalid'=>'Пожалуйста, укажите и день, и месяц, и год.')),
      'photo'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'phonenumber_city' => new sfValidatorString(array('max_length' => 20, 'required' => false),array('invalid'=>"Вы неверно указали номер телефона.")),
      'skype'            => new sfValidatorString(array('max_length' => 255, 'required' => false, 'trim'=>true)),
      'address'          => new sfValidatorString(array('required' => false)),
      'occupation'       => new sfValidatorString(array('max_length' => 255, 'trim'=>true)),
      'settings'         => new sfValidatorString(array('required' => false)),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
      'core_id'          => new sfValidatorInteger(array('required' => false)),
      'group_list'       => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'GuardGroup', 'required' => false)),
      'permission_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'GuardPermission', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('guard_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

  }  

}
