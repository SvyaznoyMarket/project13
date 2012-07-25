<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Trushina
 * Date: 13.07.12
 * Time: 12:29
 * To change this template use File | Settings | File Templates.
 */
class OrderRepository
{

    /**
     * @param string $token
     * @return null|OrderEntity
     */
    public function getByUser($userId)
    {
        if (!$userId) {
            return null;
        }
        $params = array('user_id' => $userId, 'expand' => array('credit', 'product', 'payment', 'service', 'delivery'));
        $q = new CoreQuery('order.get', $params);
        $list = $q->getResult();
//        print_r($list);

        if (empty($list) || !is_array($list) || empty($list[0])) {
            return null;
        }
        $order = array();
        foreach ($list as $data) {
            $order[] = new OrderEntity($data);
        }
        return $order;
    }

  /** @TODO подумать над переводом на запрос к ядру */
  public function getStatusList(){
    return array(
      array('id' => 1,   'token' => 'created',   'name' => 'Новый заказ'),
      array('id' => 2,   'token' => 'confirmed', 'name' => 'Подтвержден'),
      array('id' => 3,   'token' => 'assembled', 'name' => 'Собран на складе'),
      array('id' => 4,   'token' => 'delivery',  'name' => 'Доставляется'),
      array('id' => 5,   'token' => 'received',  'name' => 'Выполнен'),
      array('id' => 100, 'token' => 'cancelled', 'name' => 'Отменен'),
    );
  }


}
