<?php

/**
 * Order filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseOrderFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'token'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'type_id'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'user_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'payment_method_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PaymentMethod'), 'add_empty' => true)),
      'payment_status_id'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'payment_details'        => new sfWidgetFormFilterInput(),
      'sum'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_legal'               => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'region_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'add_empty' => true)),
      'shop_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Shop'), 'add_empty' => true)),
      'store_id'               => new sfWidgetFormFilterInput(),
      'is_delivery'            => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'is_paid_delivery'       => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'delivery_type_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DeliveryType'), 'add_empty' => true)),
      'delivered_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'delivery_period_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DeliveryPeriod'), 'add_empty' => true)),
      'address'                => new sfWidgetFormFilterInput(),
      'zip_code'               => new sfWidgetFormFilterInput(),
      'user_address_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('UserAddress'), 'add_empty' => true)),
      'recipient_first_name'   => new sfWidgetFormFilterInput(),
      'recipient_last_name'    => new sfWidgetFormFilterInput(),
      'recipient_middle_name'  => new sfWidgetFormFilterInput(),
      'recipient_phonenumbers' => new sfWidgetFormFilterInput(),
      'is_receive_sms'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'is_gift'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'extra'                  => new sfWidgetFormFilterInput(),
      'ip'                     => new sfWidgetFormFilterInput(),
      'step'                   => new sfWidgetFormFilterInput(),
      'created_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'core_id'                => new sfWidgetFormFilterInput(),
      'product_list'           => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Product')),
    ));

    $this->setValidators(array(
      'token'                  => new sfValidatorPass(array('required' => false)),
      'type_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'user_id'                => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'payment_method_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('PaymentMethod'), 'column' => 'id')),
      'payment_status_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'payment_details'        => new sfValidatorPass(array('required' => false)),
      'sum'                    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'is_legal'               => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'region_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Region'), 'column' => 'id')),
      'shop_id'                => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Shop'), 'column' => 'id')),
      'store_id'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_delivery'            => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'is_paid_delivery'       => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'delivery_type_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DeliveryType'), 'column' => 'id')),
      'delivered_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'delivery_period_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DeliveryPeriod'), 'column' => 'id')),
      'address'                => new sfValidatorPass(array('required' => false)),
      'zip_code'               => new sfValidatorPass(array('required' => false)),
      'user_address_id'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('UserAddress'), 'column' => 'id')),
      'recipient_first_name'   => new sfValidatorPass(array('required' => false)),
      'recipient_last_name'    => new sfValidatorPass(array('required' => false)),
      'recipient_middle_name'  => new sfValidatorPass(array('required' => false)),
      'recipient_phonenumbers' => new sfValidatorPass(array('required' => false)),
      'is_receive_sms'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'is_gift'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'extra'                  => new sfValidatorPass(array('required' => false)),
      'ip'                     => new sfValidatorPass(array('required' => false)),
      'step'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'core_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'product_list'           => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Product', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('order_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addProductListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.OrderProductRelation OrderProductRelation')
      ->andWhereIn('OrderProductRelation.product_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Order';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'token'                  => 'Text',
      'type_id'                => 'Number',
      'user_id'                => 'ForeignKey',
      'payment_method_id'      => 'ForeignKey',
      'payment_status_id'      => 'Number',
      'payment_details'        => 'Text',
      'sum'                    => 'Number',
      'is_legal'               => 'Boolean',
      'region_id'              => 'ForeignKey',
      'shop_id'                => 'ForeignKey',
      'store_id'               => 'Number',
      'is_delivery'            => 'Boolean',
      'is_paid_delivery'       => 'Boolean',
      'delivery_type_id'       => 'ForeignKey',
      'delivered_at'           => 'Date',
      'delivery_period_id'     => 'ForeignKey',
      'address'                => 'Text',
      'zip_code'               => 'Text',
      'user_address_id'        => 'ForeignKey',
      'recipient_first_name'   => 'Text',
      'recipient_last_name'    => 'Text',
      'recipient_middle_name'  => 'Text',
      'recipient_phonenumbers' => 'Text',
      'is_receive_sms'         => 'Boolean',
      'is_gift'                => 'Boolean',
      'extra'                  => 'Text',
      'ip'                     => 'Text',
      'step'                   => 'Number',
      'created_at'             => 'Date',
      'updated_at'             => 'Date',
      'core_id'                => 'Number',
      'product_list'           => 'ManyKey',
    );
  }
}
