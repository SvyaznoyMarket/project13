<?php

namespace controller\Supplier;


use Http\JsonResponse;
use Http\RedirectResponse;
use Http\Request;
use Http\Response;
use View\Supplier\NewPage;

/** Регистрация нового поставщика
 * Class NewAction
 * @package controller\Supplier
 */
class NewAction {

    public function execute(Request $request) {
        $page = new NewPage();
        if (\App::user()->getEntity()) return new RedirectResponse(\App::helper()->url('supplier.cabinet'));
        $form = \App::request()->request->all();
        $success = false;
        $error = null;

        if ($form) {
            try {
                $createResult = \App::coreClientV2()->query('user/create', [], $form);
                if (is_array($createResult) && isset($createResult['token'])) {
                    $updateResult = \App::coreClientV2()->query('user/update', ['token' => $createResult['token']], ['detail' => $form['detail']]);
                }
                if (isset($updateResult['confirmed'])) $success = $updateResult['confirmed'];
            } catch (\Exception $e) {
                $error = $e->getMessage();
                \App::exception()->remove($e);
            }
        }

        if (!$request->isXmlHttpRequest()) {
            $page->setParam('success', $success);
            $page->setParam('error', $error);
            return new Response($page->show());
        } else {
            return new JsonResponse(['success' => $success, 'error' => $error]);
        }

    }

}