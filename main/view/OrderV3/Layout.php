<?php

namespace View\OrderV3;

class Layout extends \View\DefaultLayout {
    protected $layout  = 'layout-orderV3';

    public function slotFooter() {
        $client = \App::contentClient();

        $response = null;
        $client->addQuery(
            'footer_compact',
            [],
            function($data) use (&$response) {
                $response = $data;
            },
            function(\Exception $e) {
                \App::exception()->add($e);
            }
        );
        $client->execute();

        $response = array_merge(['content' => ''], (array)$response);

        return $response['content'];
    }
}