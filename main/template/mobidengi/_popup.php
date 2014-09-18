<?php return function(
    \Helper\TemplateHelper $helper,
    $code,
    $message
) {
?>

<div class="view_default js-wrapper jsModal" style="position: fixed; z-index: 1000">
    <div class="b-overlay-h">
        <div class="scheme_default shadow_on skin_default js-bubble js-widget" data-opener-element-id="stm-48" style="top: 0px;">
            <div class="b-popup-h">
<!--                <div class="b-popup-close  js-bubble-close scheme_default"></div>-->
                <div class="b-popup-head">
                    <div class="b-popup-head-h">
                        <div class="b-popup-head-title"></div>
                    </div>
                </div>
                <div class="b-popup-body">
                    <div class="b-popup-body-h js-popup-body">
                        <div class="b-popup-card-head">
                            <p class="g-font size_8 g-ui align_center">
                                <?= $message ?>
                            </p>
                        </div>
                        <? if ($code === 402) : ?>
                        <div class="b-popup-card">
                            <div class="b-card-iframe js-failPopup-child js-widget" data-xtype="Stm.ui.hl.pages.tele2.CardIframe" data-conf-host-id="iframe">
                                <iframe src="https://mycard.tele2.ru/iframe" width="952" height="330" frameborder="0" scrolling="no" class="js-tele2Page-cardIframe"></iframe>
                            </div>
                        </div>
                        <? endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="jsCloseModal jsModal" style="position: fixed; opacity: 0.7; background: #000; top: 0; left: 0; bottom: 0; right: 0; z-index: 100">
    <div class="b-overlay-h"></div>
</div>

<? } ?>
