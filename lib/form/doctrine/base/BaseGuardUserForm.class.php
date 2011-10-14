<?php

/**
 * GuardUser form base class.
 *
 * @method GuardUser getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseGuardUserForm extends BaseFormDoctrine
{
  public function setup()
  {
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
      'region_id'        => new sfWidgetFormInputText(),
      'is_legal'         => new sfWidgetFormInputCheckbox(),
      'type'             => new sfWidgetFormChoice(array('choices' => array('admin' => 'admin', 'client' => 'client', 'partner' => 'partner'))),
      'gender'           => new sfWidgetFormChoice(array('choices' => array('mail' => 'mail', 'femail' => 'femail'))),
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
      'email'            => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'phonenumber'      => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'first_name'       => new sfValidatorString(array('max_length' => 255)),
      'last_name'        => new sfValidatorString(array('max_length' => 255)),
      'middle_name'      => new sfValidatorString(array('max_length' => 255)),
      'nickname'         => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'algorithm'        => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'salt'             => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'password'         => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'is_active'        => new sfValidatorBoolean(array('required' => false)),
      'is_super_admin'   => new sfValidatorBoolean(array('required' => false)),
      'last_login'       => new sfValidatorDateTime(array('required' => false)),
      'region_id'        => new sfValidatorInteger(array('required' => false)),
      'is_legal'         => new sfValidatorBoolean(array('required' => false)),
      'type'             => new sfValidatorChoice(array('choices' => array(0 => 'admin', 1 => 'client', 2 => 'partner'), 'required' => false)),
      'gender'           => new sfValidatorChoice(array('choices' => array(0 => 'mail', 1 => 'femail'), 'required' => false)),
      'birthday'         => new sfValidatorDate(array('required' => false)),
      'photo'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'phonenumber_city' => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'skype'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'address'          => new sfValidatorString(array('required' => false)),
      'occupation'       => new sfValidatorString(array('max_length' => 255)),
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

    parent::setup();
  }

  public function getModelName()
  {
    return 'GuardUser';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['group_list']))
    {
      $this->setDefault('group_list', $this->object->Group->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['permission_list']))
    {
      $this->setDefault('permission_list', $this->object->Permission->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveGroupList($con);
    $this->savePermissionList($con);

    parent::doSave($con);
  }

  public function saveGroupList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['group_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Group->getPrimaryKeys();
    $values = $this->getValue('group_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Group', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Group', array_values($link));
    }
  }

  public function savePermissionList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['permission_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Permission->getPrimaryKeys();
    $values = $this->getValue('permission_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Permission', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Permission', array_values($link));
    }
  }

}
