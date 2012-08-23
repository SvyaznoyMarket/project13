<?php

class UnitellerPaymentProvider
{
  protected $configHolder = null;

  public function __construct(array $config)
  {
    $this->configHolder = new sfParameterHolder();
    $this->configHolder->add(myToolkit::arrayDeepMerge(sfYaml::load(sfConfig::get('sf_config_dir').'/uniteller.yml'), $config));
  }

  public function getConfig($name = null)
  {
    return null == $name ? $this->configHolder->getAll() : $this->configHolder->get($name);
  }

  public function getFormUrl() {
    return $this->configHolder->get('pay_url');
  }

  /**
   *
   * @param array $order
   * @return UnitellerPaymentForm
   */
  public function getForm(array $order)
  {
    $productInfo = array();
    $serviceInfo = array();
    //информация для комментария
    if (!empty($order['product'])) foreach($order['product'] as $productRelation){
        $productInfo[$productRelation['product_id']]['name'] = $productRelation['name'];
        $productInfo[$productRelation['product_id']]['quantity'] = $productRelation['quantity'];
        $productInfo[$productRelation['product_id']]['price'] =  number_format($productRelation['price'] * $productRelation['quantity'], 0, ',', ' ');
    }

    if (!empty($order['service'])) foreach($order['service'] as $service){
        $serviceInfo[$service['service_id']]['name'] = $service['name'];
        $serviceInfo[$service['service_id']]['quantity'] = $service['quantity'];
        $serviceInfo[$service['service_id']]['price'] = number_format($service['price'] * $service['quantity'], 0, ',', ' ');
    }
    if (count($productInfo)) {
        foreach($productInfo as $product){
            $str = $product['name'] . ' (' . $product['quantity'] . 'шт.)';
            $orderInfo[$str] = $product['price'];
        }
    }
    if (count($serviceInfo)) {
        foreach($serviceInfo as $service){
            $str = $service['name'] . ' (' . $service['quantity'] . 'шт.)';
            $orderInfo[$str] = $service['price'];
        }
    }

    //цена доставки. если есть
    if (false && $order->getDeliveryPrice() > 0 && $order->getDeliveryType() ) {
         $orderInfo["'" . $order->getDeliveryType() . "'"] = $order->getDeliveryPrice();
    }
    if (!empty($order['delivery_date']) && ('0000-00-00' != $order['delivery_date'])) $orderInfo["ДатаДоставки"] = $order['delivery_date'];
    #print_r($orderInfo);
    #exit();
    $jsonOrderInfo = json_encode($orderInfo);


    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $params = array(
      'Shop_IDP'   => $this->getConfig('shop_id'),
      'Order_IDP'  => $order['number'],
      'Subtotal_P' => $order['sum'],
    );

    $sig = $this->getSignature(myToolkit::arrayDeepMerge($params, array(
      'Lifetime'     => '',
      'Customer_IDP' => '',
      'IData'        => '',
      'password'     => $this->getConfig('password'),
    )));

    $formData = array(
      'Shop_IDP'    => $this->getConfig('shop_id'),
      'Order_IDP'   => $order['number'],
      'Subtotal_P'  => $order['sum'],
      'Signature'   => $sig,
      'URL_RETURN'  => url_for($this->getConfig('return_url'), true),
    );
    $info = array(
      'url' => $this->getConfig('pay_url'),
      'Comment' => $jsonOrderInfo
    );
    $form = new UnitellerPaymentForm($formData, $info);


    $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/uniteller.log'));
    $logger->log("Form: ".$form);
    $logger->log("Comment: ".$jsonOrderInfo);

    return $form;
  }

  public function getOrder(sfWebRequest $request)
  {
    $result = null;
    if (isset($request['Order_ID']))
    {
      $token = $request['Order_ID'];
      $result = OrderTable::getInstance()->getByToken($token);
    }

    return $result;
  }

  public function getOrderIdFromRequest($request)
  {
    return $request['Order_ID'];
  }

  public function getSignature($params)
  {
    return strtoupper(md5(implode('', $params)));
  }

  public function getPaymentResult(Order $order)
  {
    $params = array(
      'Shop_ID'         => $this->getConfig('shop_id'),
      'Login'           => $this->getConfig('login'),
      'Password'        => $this->getConfig('password'),
      'ShopOrderNumber' => $order->token,
      'Format'          => 4, // XML
    );

    $response = file_get_contents($this->getConfig('result_url').'?'.http_build_query($params));

    $xml = simplexml_load_string($response);
    $code = null;
    if (1 == strval($xml['count']))
    {
      $code = strval($xml->orders->order->response_code);
    }
    $result = $this->getResultByCode($code);

    return $result;
  }

  public function getResultByCode($code)
  {
    $responseCodes = $this->getConfig('response_code');

    $stages = $this->getConfig('stage');

    $stage = array(
      'code' => $stages['fail']['code'],
      'name' => $stages['fail']['name'],
    );
    foreach ($stages as $k => $v)
    {
      if (in_array($code, $v['response_code']))
      {
        $stage = array(
          'code' => $v['code'],
          'name' => $v['name'],
        );

        break;
      }
    }

    $return = array(
      'stage'   => $stage,
      'code'    => $code,
      'message' => isset($responseCodes[$code]) ? $responseCodes[$code] : 'Платеж не найден',
    );

    return $return;
  }
}