<?php

namespace Controller\Enterprize;

class RetailClient {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function show(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();

        $flash = $session->get('flash');
        $error = null;
        if (!empty($flash['error'])) {
            $error = $flash['error'];
            unset($flash['error']);
            $session->set('flash', $flash);
        }

        $page = new \View\Enterprize\FishkaPage();
        $page->setParam('error', $error);

        return new \Http\Response($page->show());
    }


    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     * @throws \Exception\NotFoundException
     */
    public function create(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();

        // получаем купон
        /** @var \Model\EnterprizeCoupon\Entity $coupon */
        $coupon = \RepositoryManager::enterprize()->getEntityFromPartner($request->get('keyword'));
        if ((bool)$coupon && (bool)$coupon->getToken()) {
            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.form.show', [
                'enterprizeToken' => $coupon->getToken(),
                'is_partner_coupon' => true,
                'keyword' => $request->get('keyword'),
            ]));

        } else {
            $session->set('flash', ['error' => 'Неправильный пароль']);
            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.retail.show'));
        }

        return $response;
    }
} 