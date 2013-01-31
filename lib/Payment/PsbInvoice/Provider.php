<?php

namespace Payment\PsbInvoice;

class Provider implements \Payment\ProviderInterface {
    /** @var array */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = array_merge(array(
            'contractorId' => null,
            'key'          => null,
            'payUrl'       => null,
        ), $config);
    }

    /**
     * @return string
     */
    public function getPayUrl() {
        return $this->config['payUrl'];
    }

    /**
     * @param \Model\Order\Entity $order
     * @param string              $backUrl
     * @return Form
     */
    public function getForm(\Model\Order\Entity $order, $backUrl) {
        $data = [
            'ContractorID'   => $this->config['contractorId'],
            'InvoiceID'      => $order->getNumber(),
            'Sum'            => sprintf("%01.2f", $order->getSum()),
            'PayDescription' => sprintf('Оплата заказа №%s', $order->getNumber()),
            'AdditionalInfo' => '',
            'redirect_url'   => $backUrl,
        ];

        $signature = $this->config['contractorId'] . $data['InvoiceID'] . $data['Sum'] . $data['PayDescription'];
        $signature1251 = iconv('UTF-8', 'windows-1251', $signature);
        $sign = '';

        $strKey = file_get_contents($this->config['key']);
        $keyId = openssl_get_privatekey($strKey);

        openssl_sign($signature1251, $sign, $keyId);
        $data['Signature'] = base64_encode($sign);

        $form = new Form();
        $form->fromArray($data);

        return $form;
    }
}