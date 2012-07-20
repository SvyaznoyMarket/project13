<?php

class PsbankPaymentProvider
{
  private $config = array();

  public function __construct(array $config)
  {
    $this->config = myToolkit::arrayDeepMerge(sfYaml::load(sfConfig::get('sf_config_dir').'/psbank.yml'), $config);
  }

  public function getConfig($name = null)
  {
    return null == $name ? $this->config : $this->config[$name];
  }

  /**
   *
   * @param $order
   * @return PsbankPaymentForm
   */
  public function getForm($order)
  {
    //dump($order, 1);
    $data = array(
      'AMOUNT'     => $order['sum'],
      'CURRENCY'   => 'RUB',
      'ORDER'      => $order['id'],
      'DESC'       => null,
      'TERMINAL'   => $this->config['terminal'],
      'TRTYPE'     => 1,
      'MERCH_NAME' => $this->config['merchant_name'],
      'MERCHANT'   => $this->config['merchant'],
      'EMAIL'      => null,
      'TIMESTAMP'  => gmdate('YmdHis'),
      'NONCE'      => $this->generateNonce(),
      'BACKREF'    => sfContext::getInstance()->getRouting()->generate('order_complete', array('order' => $order['number']), true),
    );

    // массив названий параметров, участвующих в формировании HMAC
    $hmacParamNames = array('AMOUNT', 'CURRENCY', 'ORDER', 'MERCH_NAME', 'MERCHANT', 'TERMINAL', 'EMAIL', 'TRTYPE', 'TIMESTAMP', 'NONCE', 'BACKREF');
    $hmac = '';
    foreach ($hmacParamNames as $hmacParamName) {
      $hmac .=
        !empty($data[$hmacParamName])
        ? (mb_strlen($data[$hmacParamName]).$data[$hmacParamName])
        : '-';
    }
    //dump($data);
    //dump($hmac);
    $data['P_SIGN'] = hash_hmac('sha1', $hmac, pack('H*', $this->config['key']));

    $form = new PsbankPaymentForm($data);

    return $form;
  }

  public function getOrderIdFromRequest($request)
  {
    return $request['order'];
  }

  private function generateNonce()
  {
    $chars = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F');
    $length = rand(16, 32);

    $return = '';
    for ($i = 0; $i < $length; $i++) {
      $return .= $chars[rand(0, 15)];
    }

    return $return;
  }
}