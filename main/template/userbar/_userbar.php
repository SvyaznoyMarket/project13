<?php
$userConfig = [];
try {
    $userConfig = (new \Controller\User\InfoAction())->getResponseData(\App::request());
} catch (\Exception $e) {
    \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['user-info', 'critical']);
}
?>

<ul class="<?= isset($class) ? $class : '' ?> js-userbar-userbar" data-user-config="<?= $page->json($userConfig) ?>">
    <?= $page->render('userbar/_userinfo') ?>
    <?= $page->render('userbar/_usercompare') ?>
    <?= $page->render('userbar/_usercart') ?>
</ul>