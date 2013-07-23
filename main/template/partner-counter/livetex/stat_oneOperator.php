<?php
/**
 * @var $page      \View\DefaultLayout
 *
 */
?>


<div class="lts_slot">

    <h2 class="lts_head bPromoCatalog_eName">LiveTex: Статистика оператора</h2>

    <div class="liveTex_stat">
        <div id="haveOnline">
            <p class="online hidden">Найдены операторы онлайн: <span id="count_opers">0</span>.</p>
            <p class="offline hidden">Нет операторов онлайн</p>
        </div>
    </div>

    <?= $htmlcontent ?>

</div>