<?php

class PsbankInvoicePaymentProvider
{
  private $config = array();

  public function __construct(array $config)
  {
    $this->config = myToolkit::arrayDeepMerge(sfYaml::load(sfConfig::get('sf_config_dir').'/psbank_invoice.yml'), $config);
  }

  public function getConfig($name = null)
  {
    return null == $name ? $this->config : $this->config[$name];
  }

  public function getFormUrl() {
    return $this->config['url'];
  }

  /**
   *
   * @param $order
   * @return PsbankInvoiceForm
   */
  public function getForm($order)
  {
    $data = array(
      'ContractorID'   => $this->config['contractor_id'],
      'InvoiceID'      => $order['number'],
      'Sum'            => sprintf("%01.2f", $order['sum']),
      'PayDescription' => sprintf('Оплата заказа №%s', $order['number']),
      'AdditionalInfo' => '',
      'redirect_url'   => sfContext::getInstance()->getRouting()->generate('order_complete', array('order' => $order['number']), true),
    );

    $signature = $this->config['contractor_id'].$data['InvoiceID'].$data['Sum'].$data['PayDescription'];
    $data['Signature'] = base64_encode(
      iconv('UTF-8', 'windows-1251', $signature)
    );

    $form = new PsbankInvoicePaymentForm($data);

    return $form;
  }

  public function getOrderIdFromRequest($request)
  {
    return $request['order'];
  }
}