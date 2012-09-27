<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Trushina
 * Date: 19.06.12
 * Time: 12:29
 * To change this template use File | Settings | File Templates.
 */
class CreditBankRepository
{

  /**
   * @static
   * @param string $categoryToken
   * @return string
   */
  static public function getCreditTypeByCategoryToken($categoryToken) {
    $knownCategories = array('electronics', 'sport', 'appliances', 'do_it_yourself', 'furniture', 'household');
    if (!in_array($categoryToken, $knownCategories)) {
      return 'another';
    }
    return $categoryToken;
  }


  /**
   * @param string $token
   * @return null|CreditBankEntity
   */
  public function getById($id)
  {
    if (!$id) {
        return null;
    }
    $params = array('id' => array($id), 'geo_id' => 1);
    $result =CoreClient::getInstance()->query('payment-method.get-credit-bank', $params);

    if (empty($result) || !is_array($result) || empty($result[0])) {
        return null;
    }

    $creditBank = new CreditBankEntity($result[0]);

    return $creditBank;
  }
    /**
     * @param string $token
     * @return null|CreditBankEntity
     */
    public function getList()
    {
        $params = array('geo_id' => 1);

        $result = CoreClient::getInstance()->query('payment-method.get-credit-bank', $params);

        if (empty($result) || !is_array($result)) {
            return null;
        }

        $creditBank = array();
        foreach ($result as $item) {
            $creditBank[] = new CreditBankEntity($item);
        }

        return $creditBank;
    }

}
