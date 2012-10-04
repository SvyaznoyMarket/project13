<?php

class DeliveryTypeRepository
{

  /**
   * @return DeliveryTypeEntity[]
   */
  public function getList(){
    $data = array(
      array(
        'id'     => 1,
        'token'    => 'standart',
        'name'     => 'курьерская доставка',
        'description' => '',
      ),
      array(
        'id'     => 2,
        'token'    => 'express',
        'name'     => 'экспресс доставка',
        'description' => '',
      ),
      array(
        'id'     => 3,
        'token'    => 'self',
        'name'     => 'самовывоз',
        'description' => '',
      ),
      array(
        'id'     => 4,
        'token'    => '',
        'name'     => 'покупка в магазине',
        'description' => '',
      ),
      array(
        'id'     => 5,
        'token'    => '',
        'name'     => 'Акция!',
        'description' => 'При оплате банковской картой связной банк - бесплатная доставка.',
      ),
    );

    $return = array();

    foreach($data as $val){
      $return[] = new DeliveryTypeEntity($val);
    }
    return $return;
  }
}