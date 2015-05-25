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
     * @return Response
     */
    public function index() {
        $page = new CabinetPage();
        $userPrices = [];
        $client = \App::fileStorageClient();

        try {
            $prices = $client->query('file/get', [], ['ui' => \App::user()->getEntity()->getUi()], 10);
            if (is_array($prices)) {
                foreach ($prices as $price) {
                    $userPrices[] = $price;
                }
            }
        } catch (\Exception $e) {
            \App::exception()->remove($e);
        }

        $page->setParam('userPrices', $userPrices);
        $page->setParam('userEntity', \App::user()->getEntity());
        return new Response($page->show());
    }

    /** Загрузка прайс-листа
     * @param Request $request
     * @return JsonResponse
     */
    public function load(Request $request) {

        $client = \App::fileStorageClient();
        $clientResponse = [];
        $files = $request->files->all();
        $localFiles = [];

        $params = [ 'token' => \App::user()->getEntity()->getToken() ];
        $data = [ 'ui' => \App::user()->getEntity()->getUi() ];

        foreach ($files as $file) {
            /** @var $file \Http\File\UploadedFile */
            $localFiles[] = $file->getRealPath();
            $data['file'] = new \CURLFile($file->getRealPath(), $file->getClientMimeType(), $file->getClientOriginalName());
            $client->addQuery('file/new', $params, $data,
                function($data) use (&$clientResponse) {
                    $clientResponse[] = $data;
                },
                null,
                10);
        }

        try {
            $client->execute();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            $clientResponse[] = $e->getMessage();
            \App::exception()->remove($e);
        }

        // На всякий случай удаляем файлы
        foreach ($localFiles as $path) {
            unlink($path);
        }

        return new JsonResponse(['success' => $success, 'result' => $clientResponse]);

    }

}