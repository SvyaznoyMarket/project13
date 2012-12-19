<?php

namespace Payment\Psb;

class Provider implements \Payment\ProviderInterface {
    /** @var array */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = array_merge(array(
            'terminal'     => null,
            'merchant'     => null,
            'merchantName' => null,
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
        $form = new Form();

        $data = array(
            'AMOUNT'     => $order->getSum(),
            'CURRENCY'   => 'RUB',
            'ORDER'      => $order->getId(),
            'DESC'       => sprintf('Заказ #%s на сумму %s руб.', $order->getNumber(), $order->getSum()),
            'TERMINAL'   => $this->config['terminal'],
            'TRTYPE'     => 1,
            'MERCH_NAME' => $this->config['merchantName'],
            'MERCHANT'   => $this->config['merchant'],
            'EMAIL'      => 'support@enter.ru',
            'TIMESTAMP'  => gmdate('YmdHis'),
            'NONCE'      => $this->generateNonce(),
            'BACKREF'    => $backUrl,
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
        $data['P_SIGN'] = hash_hmac('sha1', $hmac, pack('H*', $this->config['key']));

        $form->fromArray($data);

        return $form;
    }

    /**
     * @return string
     */
    private function generateNonce() {
        $chars = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F');
        $length = rand(16, 32);

        $return = '';
        for ($i = 0; $i < $length; $i++) {
            $return .= $chars[rand(0, 15)];
        }

        return $return;
    }
}