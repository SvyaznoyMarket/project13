<?php


namespace Controller\User;


class SubscriptionsAction {

    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse([
                'data' => $this->getData($request)
            ]);
        }

        $data = $this->getData($request);

        $page = new \View\User\SubscriptionsPage();

        return new \Http\Response($page->show());

    }

    /** Возвращает массив заказов и продуктов
     * @param \Http\Request $request
     * @return array
     */
    public function getData(\Http\Request $request) {

        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        return [];

    }

} 