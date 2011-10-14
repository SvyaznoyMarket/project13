<?php

/**
 * Order form base class.
 *
 * @method Order getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseOrderForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'token'                  => new sfWidgetFormInputText(),
      'type_id'                => new sfWidgetFormInputText(),
      'user_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'payment_method_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PaymentMethod'), 'add_empty' => true)),
      'payment_status_id'      => new sfWidgetFormInputText(),
      'payment_details'        => new sfWidgetFormTextarea(),
      'sum'                    => new sfWidgetFormInputText(),
      'is_legal'               => new sfWidgetFormInputCheckbox(),
      'region_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'add_empty' => true)),
      'shop_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Shop'), 'add_empty' => true)),
      'store_id'               => new sfWidgetFormInputText(),
      'is_delivery'            => new sfWidgetFormInputCheckbox(),
      'is_paid_delivery'       => new sfWidgetFormInputCheckbox(),
      'delivery_type_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DeliveryType'), 'add_empty' => true)),
      'delivered_at'           => new sfWidgetFormDateTime(),
      'delivery_period_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DeliveryPeriod'), 'add_empty' => true)),
      'address'                => new sfWidgetFormTextarea(),
      'zip_code'               => new sfWidgetFormInputText(),
      'user_address_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('UserAddress'), 'add_empty' => true)),
      'recipient_first_name'   => new sfWidgetFormInputText(),
      'recipient_last_name'    => new sfWidgetFormInputText(),
      'recipient_middle_name'  => new sfWidgetFormInputText(),
      'recipient_phonenumbers' => new sfWidgetFormInputText(),
      'is_receive_sms'         => new sfWidgetFormInputCheckbox(),
      'is_gift'                => new sfWidgetFormInputCheckbox(),
      'extra'                  => new sfWidgetFormTextarea(),
      'ip'                     => new sfWidgetFormInputText(),
      'step'                   => new sfWidgetFormInputText(),
      'created_at'             => new sfWidgetFormDateTime(),
      'updated_at'             => new sfWidgetFormDateTime(),
      'core_id'                => new sfWidgetFormInputText(),
      'product_list'           => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Product')),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'                  => new sfValidatorString(array('max_length' => 64)),
      'type_id'                => new sfValidatorInteger(array('required' => false)),
      'user_id'                => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'required' => false)),
      'payment_method_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('PaymentMethod'), 'required' => false)),
      'payment_status_id'      => new sfValidatorInteger(array('required' => false)),
      'payment_details'        => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'sum'                    => new sfValidatorNumber(array('required' => false)),
      'is_legal'               => new sfValidatorBoolean(array('required' => false)),
      'region_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'required' => false)),
      'shop_id'                => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Shop'), 'required' => false)),
      'store_id'               => new sfValidatorInteger(array('required' => false)),
      'is_delivery'            => new sfValidatorBoolean(array('required' => false)),
      'is_paid_delivery'       => new sfValidatorBoolean(array('required' => false)),
      'delivery_type_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DeliveryType'), 'required' => false)),
      'delivered_at'           => new sfValidatorDateTime(array('required' => false)),
      'delivery_period_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DeliveryPeriod'), 'required' => false)),
      'address'                => new sfValidatorString(array('required' => false)),
      'zip_code'               => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'user_address_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('UserAddress'), 'required' => false)),
      'recipient_first_name'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'recipient_last_name'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'recipient_middle_name'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'recipient_phonenumbers' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'is_receive_sms'         => new sfValidatorBoolean(array('required' => false)),
      'is_gift'                => new sfValidatorBoolean(array('required' => false)),
      'extra'                  => new sfValidatorString(array('required' => false)),
      'ip'                     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'step'                   => new sfValidatorInteger(array('required' => false)),
      'created_at'             => new sfValidatorDateTime(),
      'updated_at'             => new sfValidatorDateTime(),
      'core_id'                => new sfValidatorInteger(array('required' => false)),
      'product_list'           => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Product', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Order', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('order[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Order';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['product_list']))
    {
      $this->setDefault('product_list', $this->object->Product->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveProductList($con);

    parent::doSave($con);
  }

  public function saveProductList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['product_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Product->getPrimaryKeys();
    $values = $this->getValue('product_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Product', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Product', array_values($link));
    }
  }

}
