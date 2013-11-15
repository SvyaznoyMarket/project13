<?php

namespace Controller\Product;

trait TrustfactorsTrait
{

    protected function getTrustfactors(&$catalogJson, $productCategoryTokens) {
        // трастфакторы
        $trustfactorTop = null;
        $trustfactorMain = null;
        $trustfactorRight = [];

        $trustfactorExcludeToken = empty($catalogJson['trustfactor_exclude_token']) ? [] : $catalogJson['trustfactor_exclude_token'];
        $excludeIntersectTokens = array_intersect($productCategoryTokens, $trustfactorExcludeToken);

        if(empty($excludeIntersectTokens)) {

            $contentClient = \App::contentClient();
            $doQuery = false;

            if(!empty($catalogJson['trustfactor_top'])) $trustfactorTop = $catalogJson['trustfactor_top'];
            if(!empty($catalogJson['trustfactor_main'])) {
                $this->trustfactorQuery($contentClient, $catalogJson['trustfactor_main'], $trustfactorMain);
                $doQuery = true;
            }

            if(!empty($catalogJson['trustfactor_right'])) {
                if(!is_array($catalogJson['trustfactor_right'])) $catalogJson['trustfactor_right'] = [$catalogJson['trustfactor_right']];
                $i = 0;
                foreach ($catalogJson['trustfactor_right'] as $trustfactorRightToken) {
                    $this->trustfactorQuery($contentClient, $trustfactorRightToken, $trustfactorRight[$i++]);
                }
                if ($i > 0) $doQuery = true;
            }

            if ($doQuery) {
                $contentClient->execute();
                if ($trustfactorRight) ksort($trustfactorRight);
            }
        }

        return [
            'top'  => $trustfactorTop,
            'main'  => $trustfactorMain,
            'right'  =>  $trustfactorRight,
        ];
    }


    private function trustfactorQuery(&$contentClient, &$source, &$elem) {
        $contentClient->addQuery(
            trim((string)$source),
            [],
            function($data) use (&$trustfactorRight, &$elem) {
                if (!empty($data['content'])) {
                    $elem = $data['content'];
                }
            },
            function(\Exception $e) {
                \App::logger()->error(sprintf('Не получено содержимое для промо-страницы %s', \App::request()->getRequestUri()));
                \App::exception()->add($e);
            }
        );
    }

}

