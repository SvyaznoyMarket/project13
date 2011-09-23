<?php

/**
 * GuardUser filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseGuardUserFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'email'            => new sfWidgetFormFilterInput(),
      'phonenumber'      => new sfWidgetFormFilterInput(),
      'first_name'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'last_name'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'middle_name'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'nickname'         => new sfWidgetFormFilterInput(),
      'algorithm'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'salt'             => new sfWidgetFormFilterInput(),
      'password'         => new sfWidgetFormFilterInput(),
      'is_active'        => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'is_super_admin'   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'last_login'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'core_id'          => new sfWidgetFormFilterInput(),
      'region_id'        => new sfWidgetFormFilterInput(),
      'type'             => new sfWidgetFormChoice(array('choices' => array('' => '', 'admin' => 'admin', 'client' => 'client', 'partner' => 'partner'))),
      'gender'           => new sfWidgetFormChoice(array('choices' => array('' => '', 'mail' => 'mail', 'femail' => 'femail'))),
      'birthday'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'photo'            => new sfWidgetFormFilterInput(),
      'phonenumber_city' => new sfWidgetFormFilterInput(),
      'skype'            => new sfWidgetFormFilterInput(),
      'address'          => new sfWidgetFormFilterInput(),
      'occupation'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'settings'         => new sfWidgetFormFilterInput(),
      'created_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'group_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'GuardGroup')),
      'permission_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'GuardPermission')),
    ));

    $this->setValidators(array(
      'email'            => new sfValidatorPass(array('required' => false)),
      'phonenumber'      => new sfValidatorPass(array('required' => false)),
      'first_name'       => new sfValidatorPass(array('required' => false)),
      'last_name'        => new sfValidatorPass(array('required' => false)),
      'middle_name'      => new sfValidatorPass(array('required' => false)),
      'nickname'         => new sfValidatorPass(array('required' => false)),
      'algorithm'        => new sfValidatorPass(array('required' => false)),
      'salt'             => new sfValidatorPass(array('required' => false)),
      'password'         => new sfValidatorPass(array('required' => false)),
      'is_active'        => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'is_super_admin'   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'last_login'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'core_id'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'region_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'type'             => new sfValidatorChoice(array('required' => false, 'choices' => array('admin' => 'admin', 'client' => 'client', 'partner' => 'partner'))),
      'gender'           => new sfValidatorChoice(array('required' => false, 'choices' => array('mail' => 'mail', 'femail' => 'femail'))),
      'birthday'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'photo'            => new sfValidatorPass(array('required' => false)),
      'phonenumber_city' => new sfValidatorPass(array('required' => false)),
      'skype'            => new sfValidatorPass(array('required' => false)),
      'address'          => new sfValidatorPass(array('required' => false)),
      'occupation'       => new sfValidatorPass(array('required' => false)),
      'settings'         => new sfValidatorPass(array('required' => false)),
      'created_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'group_list'       => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'GuardGroup', 'required' => false)),
      'permission_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'GuardPermission', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('guard_user_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addGroupListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.GuardUserGroup GuardUserGroup')
      ->andWhereIn('GuardUserGroup.group_id', $values)
    ;
  }

  public function addPermissionListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.GuardUserPermission GuardUserPermission')
      ->andWhereIn('GuardUserPermission.permission_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'GuardUser';
  }

  public function getFields()
  {
    return array(
      'id'               => 'Number',
      'email'            => 'Text',
      'phonenumber'      => 'Text',
      'first_name'       => 'Text',
      'last_name'        => 'Text',
      'middle_name'      => 'Text',
      'nickname'         => 'Text',
      'algorithm'        => 'Text',
      'salt'             => 'Text',
      'password'         => 'Text',
      'is_active'        => 'Boolean',
      'is_super_admin'   => 'Boolean',
      'last_login'       => 'Date',
      'core_id'          => 'Number',
      'region_id'        => 'Number',
      'type'             => 'Enum',
      'gender'           => 'Enum',
      'birthday'         => 'Date',
      'photo'            => 'Text',
      'phonenumber_city' => 'Text',
      'skype'            => 'Text',
      'address'          => 'Text',
      'occupation'       => 'Text',
      'settings'         => 'Text',
      'created_at'       => 'Date',
      'updated_at'       => 'Date',
      'group_list'       => 'ManyKey',
      'permission_list'  => 'ManyKey',
    );
  }
}
