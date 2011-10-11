<?php

/**
 * ServiceCategory form base class.
 *
 * @method ServiceCategory getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseServiceCategoryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'root_id'           => new sfWidgetFormInputText(),
      'core_parent_id'    => new sfWidgetFormInputText(),
      'lft'               => new sfWidgetFormInputText(),
      'rgt'               => new sfWidgetFormInputText(),
      'level'             => new sfWidgetFormInputText(),
      'token'             => new sfWidgetFormInputText(),
      'name'              => new sfWidgetFormInputText(),
      'is_active'         => new sfWidgetFormInputCheckbox(),
      'core_id'           => new sfWidgetFormInputText(),
      'product_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductType')),
      'service_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Service')),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'root_id'           => new sfValidatorInteger(),
      'core_parent_id'    => new sfValidatorInteger(array('required' => false)),
      'lft'               => new sfValidatorInteger(array('required' => false)),
      'rgt'               => new sfValidatorInteger(array('required' => false)),
      'level'             => new sfValidatorInteger(array('required' => false)),
      'token'             => new sfValidatorString(array('max_length' => 255)),
      'name'              => new sfValidatorString(array('max_length' => 255)),
      'is_active'         => new sfValidatorBoolean(array('required' => false)),
      'core_id'           => new sfValidatorInteger(array('required' => false)),
      'product_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductType', 'required' => false)),
      'service_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Service', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'ServiceCategory', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('service_category[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServiceCategory';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['product_type_list']))
    {
      $this->setDefault('product_type_list', $this->object->ProductType->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['service_list']))
    {
      $this->setDefault('service_list', $this->object->Service->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveProductTypeList($con);
    $this->saveServiceList($con);

    parent::doSave($con);
  }

  public function saveProductTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['product_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->ProductType->getPrimaryKeys();
    $values = $this->getValue('product_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('ProductType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('ProductType', array_values($link));
    }
  }

  public function saveServiceList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['service_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Service->getPrimaryKeys();
    $values = $this->getValue('service_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Service', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Service', array_values($link));
    }
  }

}
