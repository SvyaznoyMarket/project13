<?php

namespace View\OrderV3;

use Session\AbTest\ABHelperTrait;

class Layout extends \View\DefaultLayout {
    use ABHelperTrait;

    protected $layout  = 'layout-orderV3';

    public function slotBodyClassAttribute() {
        return self::isOrderWithCart() ? 'order-new' : '';
    }

    public function slotOrderHead() {
        return \App::closureTemplating()->render('order-v3-new/__head', ['step' => 1, 'withCart' => self::isOrderWithCart()]);
    }

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

        // Реактив (adblender) SITE-5718
        call_user_func(function() use ($routeName, &$html) {
            if (!\App::config()->partners['Adblender']['enabled']) return;

            $template = '<div id="adblenderJS" class="jsanalytics" data-value="{{dataValue}}"></div>';
            $dataValue = [];
            if ('orderV3.complete' === $routeName) {
                return;
            } else if ($routeName == 'order' || $routeName == 'orderV3') {
                $dataValue['type'] = 'cart';
            } else {
                $dataValue['type'] = 'default';
            }

            $html .= strtr($template, [
                '{{dataValue}}' => $this->json($dataValue),
            ]);
        });

        // Alexa
        if (\App::config()->partners['alexa']['enabled']) {
            $html .= '<div id="AlexaJS" class="jsanalytics"></div><noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=mPO9i1acVE000x" style="display:none" height="1" width="1" alt="" /></noscript>';
        }

        if (\App::config()->partners['facebook']['enabled']) {
            $html .= strtr('<div id="facebookJs" class="jsanalytics" data-value="{{dataValue}}"></div>', [
                '{{dataValue}}' => $this->json(['id' => \App::config()->facebookOauth->clientId]),
            ]);
        }

        // Livetex chat
        $html .= $this->tryRender('partner-counter/livetex/_slot_liveTex');

        // SociaPlus
        $html .= '<div id="sociaPlusJs" class="jsanalytics"></div>';

        // Передаем в Actionpay все данные по заказам
        $html .= '<div id="ActionPayJS" data-vars="' . $this->json((new \View\Partners\ActionPay($routeName, $this->params))->execute()) . '" class="jsanalytics"></div>';

        $html .= $this->googleAnalyticsJS();

        return $html;
    }
}