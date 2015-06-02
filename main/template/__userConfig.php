<?php
return function(
    \Helper\TemplateHelper $helper
) {
    $userConfig = [];
    try {
        $userConfig = (new \Controller\User\InfoAction())->getResponseData(\App::request());
    } catch (\Exception $e) {
        \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['user-info', 'critical']);
    }
?>

    <span class="js-userConfig" data-value="<?= $helper->json($userConfig) ?>" style="display: none;"></span>

    <?= $helper->jsonInScriptTag(\App::partner()->setPartner(), 'lastPartnerJSON') ?>

<? };