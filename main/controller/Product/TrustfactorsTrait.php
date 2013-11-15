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
            if(!empty($catalogJson['trustfactor_top'])) $trustfactorTop = $catalogJson['trustfactor_top'];
            if(!empty($catalogJson['trustfactor_main'])) {
                \App::contentClient()->addQuery(
                    trim((string)$catalogJson['trustfactor_main']),
                    [],
                    function($data) use (&$trustfactorMain) {
                        if (!empty($data['content'])) {
                            $trustfactorMain = $data['content'];
                        }
                    },
                    function(\Exception $e) {
                        \App::logger()->error(sprintf('Не получено содержимое для промо-страницы %s', \App::request()->getRequestUri()));
                        \App::exception()->add($e);
                    }
                );
                \App::contentClient()->execute();
            }
            if(!empty($catalogJson['trustfactor_right'])) {
                if(!is_array($catalogJson['trustfactor_right'])) $catalogJson['trustfactor_right'] = [$catalogJson['trustfactor_right']];
                $i = 0;
                foreach ($catalogJson['trustfactor_right'] as $trustfactorRightToken) {
                    \App::contentClient()->addQuery(
                        trim((string)$trustfactorRightToken),
                        [],
                        function($data) use (&$trustfactorRight, $i) {
                            if (!empty($data['content'])) {
                                $trustfactorRight[$i] = $data['content'];
                            }
                        },
                        function(\Exception $e) {
                            \App::logger()->error(sprintf('Не получено содержимое для промо-страницы %s', \App::request()->getRequestUri()));
                            \App::exception()->add($e);
                        }
                    );
                    $i++;
                }
                \App::contentClient()->execute();
                ksort($trustfactorRight);
            }
        }

        return [
            'top'  => $trustfactorTop,
            'main'  => $trustfactorMain,
            'right'  =>  $trustfactorRight,
        ];
    }

}

