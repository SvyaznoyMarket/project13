<?php

namespace Curl;

class Client {
    /**
     * @param string $url
     * @param array  $data
     * @return mixed|null
     * @throws \RuntimeException
     */
    public function query($url, array $data = array()) {
        $connection = $this->create($url, $data);

        $response = null;
        $info = null;
        try {
            $response = curl_exec($connection);
            $info = curl_getinfo($connection);

            if ($info['http_code'] >= 300) {
                throw new \RuntimeException(sprintf('Неправильный код ответа %s', $info['http_code']));
            }

            curl_close($connection);
            //\App::logger('curl')->debug(array('response' => $response, 'info' => $info));
        } catch (\Exception $e) {
            curl_close($connection);
            //\App::logger('curl')->error(array('response' => $response, 'info' => $info));

            \App::exception()->add($e);
            throw $e;
        }

        return $response;
    }

    private function create($url, array $data = array()) {
        //\App::logger()->debug(sprintf('Curl %s %s %s', (bool)$data ? 'POST' : 'GET', $url, json_encode($data, JSON_UNESCAPED_UNICODE)));

        $connection = curl_init();

        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_HEADER, 0);

        if ((bool)$data) {
            curl_setopt($connection, CURLOPT_POST, true);
            curl_setopt($connection, CURLOPT_POSTFIELDS, http_build_query($data));
            //curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        }

        return $connection;
    }
}