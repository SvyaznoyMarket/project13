<?php

namespace Controller\Product;

trait TrustfactorsTrait
{

    /**
     * Tрастфакторы: Подготовим. Проверим. Покажем.
     *
     * @param $catalogJson
     * @param $productCategoryTokens
     * @return array
     */
    protected function getTrustfactors(&$catalogJson, $productCategoryTokens) {
        $trustfactorTop = null;
        $trustfactorMain = null;
        $trustfactorRight = [];
        $trustfactorContent = [];

        $trustfactorExcludeToken = empty($catalogJson['trustfactor_exclude_token']) ? [] : $catalogJson['trustfactor_exclude_token'];
        $excludeIntersectTokens = array_intersect($productCategoryTokens, $trustfactorExcludeToken);


        if(empty($excludeIntersectTokens)) { // Нет исключений из выбранных категорий глобально

            $contentClient = \App::contentClient();
            $doQuery = false;

            if(!empty($catalogJson['trustfactor_top'])) {
                $tfPrepared = $this->prepareTrustfactor($catalogJson['trustfactor_top'], $productCategoryTokens);
                if ($tfPrepared) $trustfactorTop = $tfPrepared;
            }

            if(!empty($catalogJson['trustfactor_main'])) {
                $tfPrepared = $this->prepareTrustfactor($catalogJson['trustfactor_main'], $productCategoryTokens);
                if ($tfPrepared) {
                    $this->trustfactorQuery($contentClient, $tfPrepared, $trustfactorMain);
                    $doQuery = true;
                }
            }

            if(!empty($catalogJson['trustfactor_right'])) {
                $tfPrepared = $this->prepareTrustfactor($catalogJson['trustfactor_right'], $productCategoryTokens);
                if ( $this->trustfactorCreateQueries($contentClient, $tfPrepared, $trustfactorRight, $productCategoryTokens) ) $doQuery = true;
            }

            if(!empty($catalogJson['trustfactor_content'])) {
                $tfPrepared = $this->prepareTrustfactor($catalogJson['trustfactor_content'], $productCategoryTokens);
                if ( $this->trustfactorCreateQueries($contentClient, $tfPrepared, $trustfactorContent, $productCategoryTokens) ) $doQuery = true;
            }

            if ($doQuery) {
                $contentClient->execute();
                if ($trustfactorRight) ksort($trustfactorRight);
                if ($trustfactorContent) ksort($trustfactorContent);
            }
        }

        return [
            'top'  => $trustfactorTop,
            'main'  => $trustfactorMain,
            'right'  =>  $trustfactorRight,
            'content' => $trustfactorContent,
        ];
    }


    /**
     * Запрос к ядру, чтобы получить содержимое трастфактора
     *
     * @param $contentClient
     * @param $source
     * @param $elem
     */
    private function trustfactorQuery(&$contentClient, &$source, &$elem) {
        if (!isset($source)) return;

        $contentClient->addQuery(
            trim((string)$source),
            [],
            function($data) use (&$trustfactorRight, &$elem) {
                if (!empty($data['content'])) {
                    $elem = $data['content'];
                }
            },
            function(\Exception $e) use ($source) {
                \App::logger()->error(sprintf('Не получено содержимое трастфактора от урла %s для страницы %s', $source, \App::request()->getRequestUri()));
                //\App::exception()->add($e);
                \App::exception()->remove($e);
            }
        );
    }


    /**
     * Преобразовывает трастфактор, проверяет и возвращает его, если можно показывать
     *
     * @param $tfFields
     * @param $productCategoryTokens
     * @return array|null
     */
    private function prepareTrustfactor($tfFields, &$productCategoryTokens) {
        if ( empty($tfFields) ) {
            return null;
        } elseif ( !is_array($tfFields) ) {
            return $tfFields;
        }

        $hasExclude = isset($tfFields['exclude_tokens']);
        $hasSrc = isset($tfFields['src']);
        $excludeIntersectTokens = null;

        if ( $hasExclude || $hasSrc ) {

            if ( $hasExclude ) {
                $excludeIntersectTokens = array_intersect( $productCategoryTokens, $tfFields['exclude_tokens'] );
                unset($tfFields['exclude_tokens']);
            }

            if ( empty($excludeIntersectTokens) ) { // Нет исключений из выбранных категорий локально
                if ( $hasSrc ) {
                    $tfFields = $tfFields['src'];
                }else{
                    reset($tfFields);
                    $tfFields = key($tfFields);
                }
            }else{
                return null; // Есть локально исключениe, не показываем трастфактор
            }

        }

        return $tfFields;
    }


    /**
     * @param $contentClient
     * @param $tfPrepared
     * @param array     $trustfactorContent
     * @return bool
     */
    private function trustfactorCreateQueries(&$contentClient, &$tfPrepared, &$trustfactorContent, &$productCategoryTokens) {
        $doQuery = false;
        if ($tfPrepared) {
            $i = 0;
            if( !is_array($tfPrepared) ) {
                $tfPrepared = array($tfPrepared);
            }
            foreach ($tfPrepared as $tfElemToken => $tfElemValue) {
                $tfElemPrepared = $this->prepareTrustfactor($tfElemValue, $productCategoryTokens);
                $forQuery = $tfElemPrepared ?: $tfElemToken;
                $this->trustfactorQuery($contentClient, $forQuery, $trustfactorContent[$i++]);
            }
            if ($i > 0) $doQuery = true;
        }
        return $doQuery;
    }

}

