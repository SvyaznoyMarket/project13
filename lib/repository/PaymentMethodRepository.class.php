<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 05.06.12
 * Time: 12:29
 * To change this template use File | Settings | File Templates.
 */
class PaymentMethodRepository
{

  /**
   * @param string $token
   * @return null|ServiceEntity
   */
  public function getById($id)
  {
    $params = array('id' => $id);
    $result =CoreClient::getInstance()->query('payment-method.get', $params);

    if (empty($result) || !is_array($result) || empty($result[0])) {
        return null;
    }

    $paymentMethod = new PaymentMethodEntity($result[0]);

    return $paymentMethod;
  }
    /**
     * @param string $token
     * @return null|ServiceEntity
     */
    public function getList()
    {
        $params = array('geo_id' => 1);

        $result = CoreClient::getInstance()->query('payment-method.get', $params);

        if (empty($result) || !is_array($result)) {
            return null;
        }

        $paymentMethod = array();
        foreach ($result as $item) {
            $paymentMethod[] = new PaymentMethodEntity($item);
        }

        return $paymentMethod;
    }

}
