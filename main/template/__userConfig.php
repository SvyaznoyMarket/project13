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

    <? /* Установка партнера (plain javascript */ ?>
    <? $partnerParams = \App::partner()->setPartner() ?>
    <? if (isset($partnerParams['lastPartner'])) : ?>
    <script>
        +function(){
            try {
                var cookie = <?= json_encode($partnerParams['cookie'], JSON_UNESCAPED_UNICODE) ?>;

                document.body.addEventListener('click', function () {

                    document.cookie = 'last_partner=<?= $partnerParams['lastPartner'] ?>; path=/; expires=' + (function() {
                        var date = (new Date());
                        date.setSeconds(date.getSeconds() + <?= json_encode($partnerParams['lastPartnerCookieTime'], JSON_UNESCAPED_UNICODE) ?>);
                        return date.toUTCString();
                    })();
                    console.info('[PARTNER] Установлен партнер <?= $partnerParams['lastPartner'] ?>');

                    for (var i in cookie) {
                        if (cookie.hasOwnProperty(i)) {
                            document.cookie = cookie[i]['name'] + '=' + cookie[i]['value'] + '; path=/; expires=' + (function() {
                                var date = (new Date());
                                date.setSeconds(date.getSeconds() + cookie[i]['time']);
                                return date.toUTCString();
                            })();
                        }
                    }
                    console.info('[PARTNER] Установлены куки партнера', cookie);
                })
            } catch (e) {
                console.warn('[PARTNER] Ошибка установки партнера')
            }
        }();
    </script>
    <? endif ?>

<? };