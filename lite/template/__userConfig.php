<?php
return function(
    \Helper\TemplateHelper $helper
) {
    $userConfig = [];
    try {
        $userConfig = (new \Controller\User\InfoAction())->getResponseData(\App::request());
        // Переименуем корзину
        $userConfig['cart']['products'] = isset($userConfig['cartProducts']) ? $userConfig['cartProducts'] : [];
        unset($userConfig['cartProducts']);
        // Добавим избранное
        $userConfig['favourite'] = \App::session()->get(\App::config()->session['favouriteKey'], []);

    } catch (\Exception $e) {
        \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['user-info', 'critical']);
    }
    ?>

    <script>
        modules.define('enter.user', [], function(provide){
            provide(<?= json_encode($userConfig, JSON_UNESCAPED_UNICODE) ?>)
        })
    </script>

<? };