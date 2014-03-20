<?php

namespace Controller\Enterprize;

class ShowAction {

    public function execute(\Http\Request $request, $enterprizeToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        /** @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null */
        $enterpizeCoupon = null;
        $limit = null;
        if ($enterprizeToken) {
            \App::dataStoreClient()->addQuery('enterprize/coupon-type.json', [], function($data) use (&$enterpizeCoupon, $enterprizeToken) {
                foreach ((array)$data as $item) {
                    if ($enterprizeToken == $item['token']) {
                        $enterpizeCoupon = new \Model\EnterprizeCoupon\Entity($item);
                    }
                }
            });

            // получаем лимит для купона
            \App::coreClientV2()->addQuery('coupon/limit', [], ['list' => [$enterprizeToken]], function($data) use (&$limit, $enterprizeToken){
                if ((bool)$data && isset($data['detail'][$enterprizeToken])) {
                    $limit = (int)$data['detail'][$enterprizeToken];
                }
            }, function(\Exception $e) {
                \App::logger()->error($e->getMessage(), ['enterprize']);
                \App::exception()->remove($e);
            });
            \App::coreClientV2()->execute();
        }

        if (!$enterpizeCoupon) {
            throw new \Exception\NotFoundException(sprintf('Купон @%s не найден.', $enterprizeToken));
        }

        $page = new \View\Enterprize\ShowPage();
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('limit', $limit);

        return new \Http\Response($page->show());
    }
}