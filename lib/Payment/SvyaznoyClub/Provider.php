<?php

namespace Payment\SvyaznoyClub;

use \Payment\SvyaznoyClub\Form;
use \Model\Order\Entity as Order;

class Provider implements \Payment\ProviderInterface {
    /** @var Form */
    protected $form;
    /** @var  string */
    protected $payUrl;

    /**
     * @param Form      $form
     * @param string    $payUrl
     */
    public function __construct(Form $form) {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function getPayUrl() {
        return $this->payUrl;
    }

    /**
     * @param $payUrl
     */
    public function setPayUrl($payUrl) {
        $this->payUrl = $payUrl;
    }

    /**
     * @param Order $order
     * @param null  $backUrl
     * @return Form
     */
    public function getForm(Order $order, $backUrl = null) {
        return $this->form;
    }
} 