<?php

namespace controller\Supplier;


use Exception\AccessDeniedException;
use Http\Request;
use Http\Response;
use Http\JsonResponse;
use View\Supplier\CabinetPage;

/** Кабинет поставщика
 * Class CabinetAction
 * @package controller\Supplier
 */
class CabinetAction {

    public function __construct() {
        if (!\App::user()->getEntity()) throw new AccessDeniedException();
    }

    /** Index-страница
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        $page = new CabinetPage();
        return new Response($page->show());
    }

    /** Загрузка прайс-листа
     * @param Request $request
     * @return JsonResponse
     */
    public function load(Request $request) {

    }

    // Тестирование
    public function loadTest(){
        $clientResponse = null;
        $client = \App::fileStorageClient();
        $page = new CabinetPage();

        $params = [
            'token' => \App::user()->getEntity()->getToken()
        ];

        $data = [
            //'ui'    => \App::user()->getEntity()->getUi()
        ];

        $data['file'] = new \CURLFile('apple-touch-icon.png', 'image/png', 'test_name');

        try {
            $clientResponse = $client->query('file/new', $params, $data);
        } catch (\Exception $e) {
            $clientResponse = $e->getMessage();
            \App::exception()->remove($e);
        }

        return new JsonResponse($clientResponse);
    }

}