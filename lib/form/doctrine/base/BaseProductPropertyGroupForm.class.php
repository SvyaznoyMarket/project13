<?php

/**
 * ProductPropertyGroup form base class.
 *
 * @method ProductPropertyGroup getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductPropertyGroupForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'name'            => new sfWidgetFormInputText(),
      'product_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProductType'), 'add_empty' => false)),
      'position'        => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'property_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductProperty')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'product_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ProductType'))),
      'position'        => new sfValidatorInteger(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
      'property_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductProperty', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_property_group[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductPropertyGroup';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['property_list']))
    {
      $this->setDefault('property_list', $this->object->Property->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->savePropertyList($con);

    parent::doSave($con);
  }

  public function savePropertyList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['property_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Property->getPrimaryKeys();
    $values = $this->getValue('property_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Property', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Property', array_values($link));
    }
  }

}
