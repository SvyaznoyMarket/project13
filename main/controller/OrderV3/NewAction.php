<?php

namespace Controller\OrderV3;

use Http\RedirectResponse;
use \Model\OrderDelivery\ValidateException;

class NewAction extends OrderV3 {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\OrderV3\NewPage();

        try {

            if ($request->isMethod('POST')) {
                $post = $request->request->all();
                (new DeliveryAction())->getSplit();
                (new DeliveryAction())->getSplit($post);
                return new RedirectResponse(\App::router()->generate('orderV3.delivery'));
            }

            $this->session->remove($this->splitSessionKey);

            // testing purpose only
            (new DeliveryAction())->getSplit();

        } catch (ValidateException $e) {
            $page->setParam('error', $e->getMessage());
        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e->getMessage(), ['curl', 'cart/split']);

            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', 'CORE: '.$e->getMessage());
            $page->setParam('step', 1);

            return new \Http\Response($page->show(), 500);
        } catch (\Exception $e) {
            \App::logger()->error($e->getMessage(), ['cart/split']);

            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', $e->getMessage());
            $page->setParam('step', 1);

            return new \Http\Response($page->show(), 500);
        }

        $bonusCards = (new \Model\Order\BonusCard\Repository($this->client))->getCollection();

//        for testing
//        $bonusCards[] = reset($bonusCards);


        $page->setParam('user', $this->user);
        $page->setParam('bonusCards', $bonusCards);

        return new \Http\Response($page->show());
    }
}