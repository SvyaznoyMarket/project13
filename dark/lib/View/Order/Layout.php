<?php

namespace View\Order;

class Layout extends \View\DefaultLayout {
    protected $layout  = 'layout-order';

    public function slotFooter() {
        $client = \App::contentClient();

        try {
            $response = $client->query('footer_compact');
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $response = array('content' => '');
        }

        return $response['content'];
    }
}