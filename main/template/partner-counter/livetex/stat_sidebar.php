<?php
/**
 * @var $page      \View\DefaultLayout
 *
 */
?>

<div class="lts_sidebar bProductSection__eRight right">
    <aside>
        <div class="bWidgetBuy mWidget">
            <? if ( !empty($aside_menu) ): ?>
                <h3>Навигация</h3>
            <ul>
                <? foreach( $aside_menu as $item ) { ?>
                    <li>
                        <a href="<?= $item['link'] ?>"><?= $item['name'] ?></a>
                    </li>
                <? } ?>
            </ul>
            <hr/>
            <? endif; ?>


            <h3>Используемые данные</h3>
            <form action="/livetex-statistics" method="get">
                <? if ($stat_params): ?>
                <ul>
                    <? foreach($stat_params as $key => $item): ?>
                        <li>
                            <p>
                                <label for="<?= $key; ?>"><?= $item['descr']; ?></label>
                                <input type="text" name="<?= $key; ?>" value="<?= $item['value']; ?>" />
                            </p>
                        </li>
                    <? endforeach; ?>
                    <input type="submit" value="Обновить">
                </ul>
                <? endif; ?>
            </form>


            <hr/>
            <p>
                <a href="https://billing.livetex.ru/auth/" title="">Перейти в биллинговую систему LiveTex</a>
            </p>

        </div>
    </aside>
</div>