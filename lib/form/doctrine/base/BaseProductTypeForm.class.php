<?php

/**
 * ProductType form base class.
 *
 * @method ProductType getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'name'                  => new sfWidgetFormInputText(),
      'rating_type_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('RatingType'), 'add_empty' => true)),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
      'service_category_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ServiceCategory')),
      'product_category_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory')),
      'property_list'         => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductProperty')),
      'property_group_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductPropertyGroup')),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'                  => new sfValidatorString(array('max_length' => 255)),
      'rating_type_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('RatingType'), 'required' => false)),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
      'service_category_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ServiceCategory', 'required' => false)),
      'product_category_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory', 'required' => false)),
      'property_list'         => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductProperty', 'required' => false)),
      'property_group_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductPropertyGroup', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductType';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['service_category_list']))
    {
      $this->setDefault('service_category_list', $this->object->ServiceCategory->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['product_category_list']))
    {
      $this->setDefault('product_category_list', $this->object->ProductCategory->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['property_list']))
    {
      $this->setDefault('property_list', $this->object->Property->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['property_group_list']))
    {
      $this->setDefault('property_group_list', $this->object->PropertyGroup->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveServiceCategoryList($con);
    $this->saveProductCategoryList($con);
    $this->savePropertyList($con);
    $this->savePropertyGroupList($con);

    parent::doSave($con);
  }

  public function saveServiceCategoryList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['service_category_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->ServiceCategory->getPrimaryKeys();
    $values = $this->getValue('service_category_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('ServiceCategory', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('ServiceCategory', array_values($link));
    }
  }

  public function saveProductCategoryList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['product_category_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->ProductCategory->getPrimaryKeys();
    $values = $this->getValue('product_category_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('ProductCategory', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('ProductCategory', array_values($link));
    }
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

  public function savePropertyGroupList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['property_group_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->PropertyGroup->getPrimaryKeys();
    $values = $this->getValue('property_group_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('PropertyGroup', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('PropertyGroup', array_values($link));
    }
  }

}
