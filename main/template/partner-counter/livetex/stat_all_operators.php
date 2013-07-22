<?php
/**
 * @var $page      \View\DefaultLayout
 *
 */
?>
<div class="clear"></div>
<noscript><p>Javascript must be enabled for the correct page display</p></noscript>

<!-- livetext statistics -->
<div id="promoCatalog" class="bPromoCatalog">

    <h2 class="bPromoCatalog_eName">LiveTex: Статистика операторов</h2>


    <div class="liveTex_stat">
        <div id="haveOnline">
            <p class="online hidden">Найдены операторы онлайн: <span id="count_opers">0</span>.</p>
            <p class="offline hidden">Нет операторов онлайн</p>
        </div>
        <div class="operators_count_wr">
            <p class="operators_count"><span>Всего операторов: </span><?= $operators_count_html ?></p>
        </div>
        <div id="operators_wr" class="operators_stat">
            <ul id="operators">
                <?= $operators_html; ?>
            </ul>
        </div>
    </div>



</div>
<!-- end livetext statistics -->
