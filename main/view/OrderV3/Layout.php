<?php

namespace View\OrderV3;

class Layout extends \View\DefaultLayout {
    protected $layout  = 'layout-orderV3';

    public function slotPartnerCounter()
    {
        if (!\App::config()->analytics['enabled']) return '';

        $html = '';
        $routeName = \App::request()->attributes->get('route');
        $routeToken = \App::request()->attributes->get('token');

        if ('subscribe_friends' == $routeToken) {
            $html .= $this->tryRender('partner-counter/_actionpay_subscribe');
            $html .= $this->tryRender('partner-counter/_cityAds_subscribe');
        }

        // Alexa
        if (\App::config()->partners['alexa']['enabled']) {
            $html .= '<div id="AlexaJS" class="jsanalytics"></div><noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=mPO9i1acVE000x" style="display:none" height="1" width="1" alt="" /></noscript>';
        }

        // Livetex chat
        $html .= $this->tryRender('partner-counter/livetex/_slot_liveTex');

        // SociaPlus
        $html .= '<div id="sociaPlusJs" class="jsanalytics"></div>';

        if ($routeName != 'orderV3.complete') {
            $actionpayData = (new \View\Partners\ActionPay($routeName, $this->params))->execute();
        } else {
            $actionpayData = \App::partner()->getName() == 'actionpay' ?  (new \View\Partners\ActionPay($routeName, $this->params))->execute() : [];
        }

        $html .= '<div id="ActionPayJS" data-vars="' . $this->json($actionpayData) . '" class="jsanalytics"></div>';

        $html .= $this->googleAnalyticsJS();

        return $html;
    }
}