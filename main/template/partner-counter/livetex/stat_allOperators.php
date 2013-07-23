<?php
/**
 * @var $page      \View\DefaultLayout
 *
 */
?>

<div class="lts_slot">

    <h2 class="lts_head bPromoCatalog_eName">LiveTex: Статистика операторов</h2>

    <div class="liveTex_stat">
        <div id="haveOnline">
            <p class="online hidden">Найдены операторы онлайн: <span id="count_opers">0</span>.</p>
            <p class="offline hidden">Нет операторов онлайн</p>
        </div>
        <? if ( isset($operators_count_html) ): ?>
        <div class="operators_count_wr">
            <p class="operators_count"><span>Всего операторов: </span> <?= $operators_count_html ?></p>
        </div>
        <? endif; ?>
        <div id="operators_wr" class="operators_stat">
            <?= $htmlcontent ?>
        </div>
    </div>

</div>


