<?php

namespace Controller;

class CurlAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        if ('local' !== \App::$env) {
            throw new \Exception\NotFoundException();
        }

        \App::logger()->debug('Exec ' . __METHOD__);

        $url = trim((string)$request->get('url'));
        $data = (array)$request->get('data');

        try {
            $result = \App::curl()->query($url, $data);
        } catch (\Exception $e) {
            $result = (string)$e;
        }


        return new \Http\Response(htmlentities(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)));
    }
}
