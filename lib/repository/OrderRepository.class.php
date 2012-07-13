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
        $params = array('user_id' => $userId, 'expand' => array('credit'));
        $q = new CoreQuery('order.get', $params);
        $list = $q->getResult();
        //print_r($list);

        if (empty($list) || !is_array($list) || empty($list[0])) {
            return null;
        }
        $order = array();
        foreach ($list as $data) {
            $order[] = new OrderEntity($data);
            $a = new OrderEntity($data);
            print_r($data);
            print_r($a);
            die();
        }

        print_r($order);
        return $order;
    }


}
