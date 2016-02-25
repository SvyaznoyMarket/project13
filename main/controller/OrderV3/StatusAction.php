<?php

namespace Controller\OrderV3;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class StatusAction
{
    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception|null
     */
    public function execute(\Http\Request $request)
    {
        $curl = $this->getCurl();

        $responseData = [
            'errors' => [],
            'status' => null,
            'order'  => [
                'number' => null,
            ]
        ];

        $form = [
            'number' => null,
        ];
        if (is_array($request->request->get('order'))) {
            $form = array_merge($form, $request->request->get('order'));
        }

        try {
            if (!$form['number']) {
                $responseData['errors'][] = ['field' => 'number', 'message' => 'Не указан номер заказа', 'code' => 'invalid'];
            }

            $responseData['order']['number'] = $form['number'];

            if ($responseData['errors']) {
                throw new \Exception('Форма заполнена неверно');
            }

            $getQuery = (new Query\Order\GetStatusByNumberErp($form['number']))->prepare();

            $curl->execute();

            if ($error = $getQuery->error) {
                $responseData['errors'][] = ['field' => null, 'message' => 'Не удалось получить статус заказа', 'code' => 'fatal'];
                throw $error;
            }

            $responseData['status'] = $getQuery->response->status;

        } catch (\Exception $e) {
            //\App::exception()->add($e);
            \App::logger()->error($e);
        }

        return $request->isXmlHttpRequest()
            ? new \Http\JsonResponse($responseData)
            : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
    }
}
