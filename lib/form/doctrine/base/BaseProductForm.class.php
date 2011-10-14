<?php

/**
 * Product form base class.
 *
 * @method Product getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'core_id'         => new sfWidgetFormInputText(),
      'token'           => new sfWidgetFormInputText(),
      'barcode'         => new sfWidgetFormInputText(),
      'article'         => new sfWidgetFormInputText(),
      'name'            => new sfWidgetFormInputText(),
      'type_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Type'), 'add_empty' => false)),
      'creator_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Creator'), 'add_empty' => false)),
      'group_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => true)),
      'tagline'         => new sfWidgetFormInputText(),
      'preview'         => new sfWidgetFormTextarea(),
      'view_show'       => new sfWidgetFormInputCheckbox(),
      'view_list'       => new sfWidgetFormInputCheckbox(),
      'is_instock'      => new sfWidgetFormInputCheckbox(),
      'description'     => new sfWidgetFormInputText(),
      'rating'          => new sfWidgetFormInputText(),
      'rating_quantity' => new sfWidgetFormInputText(),
      'price'           => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'news_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'News')),
      'order_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Order')),
      'category_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'core_id'         => new sfValidatorInteger(array('required' => false)),
      'token'           => new sfValidatorString(array('max_length' => 255)),
      'barcode'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'article'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'name'            => new sfValidatorString(array('max_length' => 255)),
      'type_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Type'))),
      'creator_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Creator'))),
      'group_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'required' => false)),
      'tagline'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'preview'         => new sfValidatorString(array('required' => false)),
      'view_show'       => new sfValidatorBoolean(array('required' => false)),
      'view_list'       => new sfValidatorBoolean(array('required' => false)),
      'is_instock'      => new sfValidatorBoolean(array('required' => false)),
      'description'     => new sfValidatorPass(array('required' => false)),
      'rating'          => new sfValidatorNumber(array('required' => false)),
      'rating_quantity' => new sfValidatorInteger(array('required' => false)),
      'price'           => new sfValidatorNumber(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
      'news_list'       => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'News', 'required' => false)),
      'order_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Order', 'required' => false)),
      'category_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ProductCategory', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Product', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('product[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Product';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['news_list']))
    {
      $this->setDefault('news_list', $this->object->News->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['order_list']))
    {
      $this->setDefault('order_list', $this->object->Order->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['category_list']))
    {
      $this->setDefault('category_list', $this->object->Category->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveNewsList($con);
    $this->saveOrderList($con);
    $this->saveCategoryList($con);

    parent::doSave($con);
  }

  public function saveNewsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['news_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->News->getPrimaryKeys();
    $values = $this->getValue('news_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('News', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('News', array_values($link));
    }
  }

  public function saveOrderList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['order_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Order->getPrimaryKeys();
    $values = $this->getValue('order_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Order', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Order', array_values($link));
    }
  }

  public function saveCategoryList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['category_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Category->getPrimaryKeys();
    $values = $this->getValue('category_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Category', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Category', array_values($link));
    }
  }

}
