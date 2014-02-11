<?php
/**
 * DataStore - доступ к json-файлам из CMS
 */

namespace Model\DataStore;


class Repository {
    //private $fileName;

    /**
     * @param \DataStore\Client $client
     */
    public function __construct(\DataStore\Client $client/*, $fname*/) {
        $this->client = $client;
        //$this->fileName = $fname;
    }

    /**
     * Вернёт данные json-файла $fname
     * (http://cms.enter.loc/v1/$fname)
     * Например: http://cms.enter.loc/v1/subscribe-form.json
     *
     * @param $fname
     * @return array|null
     */
    public function getData($fname) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ; DataFile ' . $fname . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;
        $data = $client->query($fname);

        return $data;
    }

    /**
     * @return Entity[]
     *//*
    public function getCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ; DataFile ' . $this->fileName . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;
        $data = $client->query($this->fileName);

        if (!isset($data['item'])) return false;

        $collection = [];

        foreach ($data['item'] as $item) {
            //$collection[] = new Entity($item);
        }
        $collection = $data;

        return $collection;
    }*/

    /**
     * @param callback      $done
     * @param callback|null $fail
     *//*
    public function prepareCollection($done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ; DataFile ' . $this->fileName . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery( $this->fileName, [], $done, $fail, \App::config()->dataStore['timeout'] * 2);
    }*/

}
