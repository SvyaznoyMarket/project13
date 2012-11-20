<?php

namespace Controller\Import;

class RegionInflectAction {
    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $xml = simplexml_load_file(\App::config()->dataDir . '/core/geo.xml');

        foreach ($xml->xpath('//row') as $row) {
            $item = array();
            foreach ($row->field as $field) {
                if ('id' == $field['name']) {
                    $item['id'] = (string)$field;
                }
                if ('name' == $field['name']) {
                    $item['name'] = (string)$field;
                }
                if ('is_state' == $field['name']) {
                    $item['is_state'] = 1 == (string)$field;
                }
            }

            $isValid = true;
            foreach (array(
                ' АО', ' мкр', ' п', ' снт', ' пгт',
                ' обл', ' д', ' с', ' нп', ' край',
                ' Респ', ' тер', ' те', ' усадьба',
                ' жилрайон', ' кп', ' рп', ' х', ' м', ' ж/д',
                ' кордон', ' рзд', ' городок', ' н', ' учебно-тренир',
                ' отделение', ' кв', 'свх ',
                ' Федеральный Округ', ' Аобл', 'ПФО', 'С-ЗФО', 'Сев-КавФО', 'СибФО', 'УФО', 'ЮФО', 'Госпиталь ', 'Подсобное Хозяйство',
                '(',
            ) as $needle) {
                if (false !== strpos($item['name'], $needle)) {
                    $isValid = false;
                    break;
                }
            }
            if (!$isValid) continue;

            $item['name'] = preg_replace('/ г$/', '', $item['name']);

            echo $item['name'] . "\n";

            $file = \App::config()->dataDir . '/inflect/region/' . $item['id'] . '.json';
            if (file_exists($file)) {
                continue;
                //unlink($file);
            }

            $response = file_get_contents('http://export.yandex.ru/inflect.xml?' . http_build_query(array(
                'name' => $item['name'],
            )));
            if (!$itemXml = simplexml_load_string($response)) continue;

            $data = array();
            foreach ($itemXml->xpath('//inflection') as $inflection) {
                $data[] = (string)$inflection;
            }

            file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }
}