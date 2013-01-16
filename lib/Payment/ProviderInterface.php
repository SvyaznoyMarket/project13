<?php

namespace Payment;

interface ProviderInterface {
    public function getPayUrl();
    public function getForm(\Model\Order\Entity $order, $backUrl);
}