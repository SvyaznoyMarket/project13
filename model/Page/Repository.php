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
     * Получает seo-теги для страницы
     * Возвращает массив
     *
     * @param $page
     * @return array
     */
    public static function getSeoJson($page = "main-page")
    {
        // формируем запрос к апи и получаем json с seo-тегами для страницы

        $seoJson = [];

        $dataStore = \App::dataStoreClient();

        $query = 'seo/' . $page . '.json';

        $dataStore->addQuery($query, [], function ($data) use (&$seoJson) {
            if($data) $seoJson = $data;
        });

        $dataStore->execute();

        return empty($seoJson) ? [] : $seoJson;
    }


}