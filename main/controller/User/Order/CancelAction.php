<?php


namespace Controller\User\Order;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class CancelAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        $curl = $this->getCurl();

        $form = [
            'id' => null,
        ];
        $form = array_merge($form, is_array($request->get('order')) ? $request->get('order') : []);
        if (!$form['id']) {
            throw new \Exception('Не передан ид заказа', 400);
        }

        $cancelQuery = new Query\Order\Cancel();
        $cancelQuery->userToken = \App::user()->getEntity()->getToken();
        $cancelQuery->id = $form['id'];
        $cancelQuery->prepare();

        $curl->execute();

        if ($error = $cancelQuery->error) {
            \App::session()->flash(['errors' => ['code' => $error->getCode(), 'message' => $error->getMessage()]]);

            \App::logger()->error(['error' => $error, 'sender' => __FILE__ . ' ' .  __LINE__], ['user.order.cancel']);

            //throw $error;
        } else if (!$cancelQuery->response->success) {
            \App::session()->flash(['errors' => ['code' => 0, 'message' => $cancelQuery->response->message]]);

            \App::logger()->error(['error' => ['message' => $cancelQuery->response->message], 'sender' => __FILE__ . ' ' .  __LINE__], ['user.order.cancel']);
        }

        $response =  new \Http\RedirectResponse($request->server->get('HTTP_REFERER') ?: \App::router()->generateUrl('user.orders'));

        return $response;
    }
}