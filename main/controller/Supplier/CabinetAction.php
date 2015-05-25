<?php

namespace controller\Supplier;


use Exception\AccessDeniedException;
use Http\Request;
use Http\Response;
use Http\JsonResponse;
use Model\Supplier\File;
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
            $prices = $client->query('file/get', [], ['ui' => \App::user()->getEntity()->getUi()]);
            if (is_array($prices)) {
                foreach ($prices as $price) {
                    $userPrices[] = new File($price);
                }
            }
        } catch (\Exception $e) {
            \App::exception()->remove($e);
        }

        // Новые прайс-листы сверху
        usort($userPrices, function(File $a, File $b) { return $a->added < $b->added; });

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
        $html = '';

        $params = [ 'token' => \App::user()->getEntity()->getToken() ];
        $data = [ 'ui' => \App::user()->getEntity()->getUi() ];

        foreach ($files as $file) {
            /** @var $file \Http\File\UploadedFile */
            $localFiles[] = $file->getRealPath();
            $data['file'] = new \CURLFile($file->getRealPath(), $file->getClientMimeType(), $file->getClientOriginalName());
            $client->addQuery('file/new', $params, $data,
                function($data) use (&$clientResponse, &$html) {
                    $file = new File($data);
                    $clientResponse[] = $file;
                    $html .= \App::templating()->render('supplier/_file', ['file' => $file]);
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

        return new JsonResponse(['success' => $success, 'result' => $clientResponse, 'html' => $html]);

    }

    /** Обновление данных о поставщике/пользователе
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request) {
        $fields = $request->request->all();
        try {
            $result = \App::coreClientV2()->query('user/update',
                ['token' => \App::user()->getEntity()->getToken()],
                ['detail' => $fields['detail']]);
        } catch (\Exception $e) {
            \App::exception()->remove($e);
        }

        return new JsonResponse([
            'success' => isset($result['confirmed']) && (bool)$result['confirmed'],
            'fields' => $fields['detail']]);
    }

}