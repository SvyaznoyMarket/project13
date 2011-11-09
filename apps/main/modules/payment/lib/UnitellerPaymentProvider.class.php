<?php

class UnitellerPaymentProvider
{
  protected $configHolder = null;

  public function __construct(array $config)
  {
    $this->configHolder = new sfParameterHolder();
    $this->configHolder->add(myToolkit::arrayDeepMerge(sfYaml::load(dirname(__FILE__).'/../config/uniteller.yml'), $config));
  }

  public function getConfig($name = null)
  {
    return null == $name ? $this->configHolder->getAll() : $this->configHolder->get($name);
  }

  public function getForm(Order $order)
  {
    $orderInfo = array(
        'id' => $order->id,
        'sum' => $order->sum,
    ); 
    foreach($order->getProductRelation() as $product){
        $orderInfo['products'][$product->product_id]['id'] = $product->product_id;
        $orderInfo['products'][$product->product_id]['quantity'] = $product->quantity;
    }
    foreach($order->getProduct() as $product){
        $orderInfo['products'][$product->id]['name'] = $product->name;
        $orderInfo['products'][$product->id]['price'] = $product->price;
    }
    #print_r($orderInfo);
    
    $jsonOrderInfo = json_encode($orderInfo);
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $params = array(
      'Shop_IDP'  => $this->getConfig('shop_id'),
      'Order_IDP' => $order->id,
      'Subtotal_P'  => $order->sum,
    );

    $sig = $this->getSignature(myToolkit::arrayDeepMerge($params, array(
      'Lifetime'     => '',
      'Customer_IDP' => '',
      'IData'        => '',
      'password'     => $this->getConfig('password'),
    )));

    $form = new UnitellerPaymentForm(array(
      'Shop_IDP'    => $this->getConfig('shop_id'),
      'Order_IDP'   => $order->id,
      'Subtotal_P'  => $order->sum,
      'Signature'   => $sig,
      'URL_RETURN'  => url_for($this->getConfig('return_url'), true),
    ), array(
      'url' => $this->getConfig('pay_url'),
      'comment' => $jsonOrderInfo,      
    ));

    return $form;
  }

  public function getOrder(sfWebRequest $request)
  {
    $id = $request['Order_ID'];

    return OrderTable::getInstance()->getById($id);
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
      'ShopOrderNumber' => $order->id,
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