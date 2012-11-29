<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Trushina
 * Date: 05.06.12
 * Time: 12:29
 * To change this template use File | Settings | File Templates.
 */
class PaymentMethodRepository
{

  /**
   * @param string $token
   * @return null|PaymentMethodEntity
   */
  public function getById($id)
  {
    if (!$id) {
        return  null;
    }
    $params = array('id' => array($id), 'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId());
    $result =CoreClient::getInstance()->query('payment-method.get', $params);

    if (empty($result) || !is_array($result) || empty($result[0])) {
        return null;
    }

    $paymentMethod = new PaymentMethodEntity($result[0]);

    return $paymentMethod;
  }
    /**
     * @param string $token
     * @return null|PaymentMethodEntity[]
     */
    public function getList()
    {
        $params = array('geo_id' => RepositoryManager::getRegion()->getDefaultRegionId());
        if ($user = sfContext::getInstance()->getUser()->getGuardUser()) {
            if ($user->getIsCorporative()) {
                $params['is_corporative'] = true;
            }
        }

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

  /**
   * Ф-я возвращает список айдишников, которые допустимы для оплаты на сайте
   * @return int[]
   */
  public function getAcceptedList() {
    $return = array(1, 4, 2, 5, 6, 8);
    if (sfConfig::get('app_certificate_enabled', false)) {
        $return[] = PaymentMethodEntity::CERTIFICATE_ID;
    }

    return $return;
  }

}
