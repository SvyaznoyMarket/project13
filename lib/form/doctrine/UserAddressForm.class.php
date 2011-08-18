<?php

/**
 * UserAddress form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserAddressForm extends BaseUserAddressForm
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    $this->widgetSchema['city_id']->setLabel('Город');

    $this->widgetSchema['name']->setLabel('Название');

    $this->widgetSchema['address']->setLabel('Адрес');

    $this->useFields(array(
      'city_id',
      'address',
      'name',
    ));

    $this->widgetSchema->setNameFormat('address[%s]');
  }

  protected function updateNameColumn($value)
  {
    if (empty($value))
    {
      $value = $this->getValue('address');
    }

    return $value;
  }
}
