<?php

namespace Controller\Enterprize;

class ShowAction {

    public function execute(\Http\Request $request, $enterprizeToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if (!(bool)$enterprizeToken) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize'));
        }

        // флаг, партнерский купон или нет
        $isPartnerCoupon = (bool)$request->get('is_partner_coupon') && (bool)$request->get('keyword');

        $client = \App::coreClientV2();
        $repository = \RepositoryManager::enterprize();

        // получение купона
        /** @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null */
        $enterpizeCoupon = null;

        // партнерский купон
        if ($isPartnerCoupon) {
            $enterpizeCoupon = $repository->getEntityFromPartner($request->get('keyword'));
        } else {
            $enterpizeCoupon = $repository->getEntityByToken($enterprizeToken);
        }

        if (!(bool)$enterpizeCoupon) {
            throw new \Exception\NotFoundException(sprintf('Купон @%s не найден.', $enterprizeToken));
        }

        // получаем лимит для купона
        $limit = null;
        try {
            $client->addQuery('coupon/limits', [], ['list' => [$enterpizeCoupon->getToken()]], function($data) use (&$limit, $enterprizeToken){
                if ((bool)$data && isset($data['detail'][$enterprizeToken])) {
                    $limit = (int)$data['detail'][$enterprizeToken];
                }
            });
            $client->execute();
        } catch (\Exception $e) {
            \App::logger()->error($e, ['enterprize']);
            \App::exception()->remove($e);
        }

        $page = new \View\Enterprize\ShowPage();
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('limit', $limit);
        $page->setParam('viewParams', ['showSideBanner' => false]);
        $page->setParam('isPartnerCoupon', $isPartnerCoupon);

        return new \Http\Response($page->show());
    }
}