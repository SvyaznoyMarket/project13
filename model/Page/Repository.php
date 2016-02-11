<?php
/**
 * Created by JetBrains PhpStorm.
 * User: juljan
 * Date: 11.7.13
 * Time: 15.01
 * To change this template use File | Settings | File Templates.
 */


namespace Model\Page;

class Repository
{
    /**
     * @deprecated
     *
     * Получает seo данные для главной страницы
     * @param $page
     * @return array
     */
    public static function getSeo()
    {
        $data = \App::scmsClient()->query('api/parameter/get-by-keys', ['keys' => ['title', 'description', 'keywords']]);

        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                $result[$item['key']] = $item['value'];
            }

            return $result;
        }

        return [];
    }
}