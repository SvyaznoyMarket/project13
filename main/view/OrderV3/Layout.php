<?php

namespace View\OrderV3;

use Session\AbTest\ABHelperTrait;

class Layout extends \View\DefaultLayout {
    use ABHelperTrait;

    protected $layout  = 'layout-orderV3';
    public $isStepDelivery = false;

    public function slotBodyClassAttribute() {
        return '';
    }

    public function slotOrderHead() {
        return \App::closureTemplating()->render('order-v3-new/__head', ['step' => 1]);
    }

    public function slotPartnerCounter()
    {
        if (!\App::config()->analytics['enabled']) return '';

        $html = '';
        $routeName = \App::request()->routeName;
        $routeToken = \App::request()->routePathVars->get('token');

        if ('subscribe_friends' == $routeToken) {
            $html .= $this->tryRender('partner-counter/_actionpay_subscribe');
            $html .= $this->tryRender('partner-counter/_cityAds_subscribe');
        }

        if (\App::config()->partners['facebook']['enabled']) {
            $html .= strtr('<div id="facebookJs" class="jsanalytics" data-value="{{dataValue}}"></div>', [
                '{{dataValue}}' => $this->json(['id' => \App::config()->facebookOauth->clientId]),
            ]);
        }

        $html .= $this->flocktoryScriptJS();

        // Livetex chat
        $html .= $this->tryRender('partner-counter/livetex/_slot_liveTex');

        // Передаем в Actionpay все данные по заказам
        $html .= '<div id="ActionPayJS" data-vars="' . $this->json((new \View\Partners\ActionPay($routeName, $this->params))->execute()) . '" class="jsanalytics"></div>';

        $html .= $this->googleAnalyticsJS();

        return $html;
    }
}