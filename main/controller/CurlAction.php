<?php

namespace Controller;

class CurlAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $url = trim((string)$request->get('url'));
        $data = (array)$request->get('data');

        $domain = implode('.', array_slice(array_pad(explode('.', parse_url($url, PHP_URL_HOST)), 2, null), -2, 2));
        if (!in_array($domain, ['enter.ru', 'ent3.ru', 'enter.loc', 'ent3.dev', 'enter-cms.loc'])) {
            throw new \Exception\NotFoundException();
        }

        try {
            $result = \App::curl()->query($url, $data);
        } catch (\Exception $e) {
            $result = (string)$e;
        }


        return new \Http\Response(htmlentities(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)));
    }
}
