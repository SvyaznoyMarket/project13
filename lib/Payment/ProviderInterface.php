<?php

namespace Payment;

interface ProviderInterface {
    public function getPayUrl();
    public function setPayUrl($payUrl);
    public function getForm(\Model\Order\Entity $order, $backUrl);
}