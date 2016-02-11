<?php

namespace Controller\UserCallback;

use EnterQuery as Query;

class CreateAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $config = \App::config()->userCallback;

        $responseData = [
            'errors' => [],
        ];

        $form = [
            'phone' => null,
        ];
        if (is_array($request->request->get('user'))) {
            $form = array_merge($form, $request->request->get('user'));
        }

        try {
            if (!$form['phone']) {
                $errors[] = ['field' => 'phone', 'message' => 'Не указан телефон', 'code' => 'invalid'];
            }

            if ($responseData['errors']) {
                throw new \Exception('Форма заполнена неверно');
            }

            $createQuery = new Query\UserCallback\Create($form['phone'], $config['from'], $config['to']);

        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        return $request->isXmlHttpRequest()
            ? new \Http\JsonResponse($responseData)
            : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
    }
}