<?php

/**
 * order_ components.
 *
 * @package    enter
 * @subpackage order_
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class order_Components extends myComponents
{
  /**
   * Executes field_region_id component
   *
   * @param string $name Название поля
   * @param mixed $value Значение поля
   */
  public function executeField_region_id()
  {
    $this->region = !empty($this->value) ? RegionTable::getInstance()->getById($this->value) : false;
    $this->displayValue = $this->region ? $this->region->name : '';
  }
  /**
   * Executes field_delivery_type_id component
   *
   * @param string $name Название поля
   * @param mixed $value Значение поля
   * @param myDoctrineCollection $deliveryTypeList Коллекция типов доставки для пользователя (с учетом региона и товаров в корзине)
   */
  public function executeField_delivery_type_id()
  {
    $choices = array();
    foreach ($this->deliveryTypes as $deliveryType)
    {
      /* @var $deliveryType DeliveryTypeEntity */

      $choices[$deliveryType->getId()] = array(
        'id'          => strtr($this->name, array('[' => '_', ']' => '_')).$deliveryType->getId(),
        'label'       => $deliveryType->getName(),
        'description' => $deliveryType->getDescription(),
        'type'        => $deliveryType->getToken(),
      );
    }

    $this->name = $this->name;
    $this->setVar('choices', $choices, true);
  }
  /**
   * Executes field_payment_method_id component
   *
   * @param string $name Название поля
   * @param mixed $value Значение поля
   */
  public function executeField_payment_method_id()
  {
    $choices = array();

    foreach (PaymentMethodTable::getInstance()->getList() as $paymentMethod)
    {
      if ('online' == $paymentMethod->token) continue;

      $choices[$paymentMethod->id] = array(
        'id'          => strtr($this->name, array('[' => '_', ']' => '_')).$paymentMethod->id,
        'label'       => $paymentMethod->name,
        'description' => $paymentMethod->description,
      );
    }

    $this->setVar('choices', $choices, true);
  }
  /**
   * Executes field_products component
   *
   */
  public function executeField_products()
  {
    date_default_timezone_set('Europe/Moscow');

    $maxWeek = 4;

    $dates = array();
    $now = time();
    $time = 1 == date('w') ? $now : strtotime('Last Monday', $now);
    foreach (range(1, 7 * $maxWeek) as $i)
    {
      $dayDiff = intval(floor(($time - $now) / 86400));

      $prefix = '';
      if (0 == $dayDiff)
      {
        $prefix = 'сегодня ';
      }
      if (1 == $dayDiff)
      {
        $prefix = 'завтра ';
      }
      if (2 == $dayDiff)
      {
        $prefix = 'послезавтра ';
      }

      $dates[] = array(
        'value'        => date('Y-m-d', $time),
        'day'          => date('j', $time),
        'dayOfWeek'    => format_date($time, 'EEE', 'ru'),
        //'name'      => $prefix.date('Y-m-d', $time),
        'displayValue' => format_date($time, 'd MMMM', 'ru'),
        'weekNum'      => floor(($i - 1) / 7) + 1,
      );

      // icrement day
      $time = strtotime("+1 day", $time);
    }

    $this->setVar('dates', $dates, true);
  }
}

