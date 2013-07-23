<?php
/**
 * @var $page      \View\DefaultLayout
 *
 */
?>

<div class="bProductSection__eRight right">
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
                    <? foreach($stat_params as $item): ?>
                        <li>
                            <p>
                                <label for="<?= $item['name']; ?>"><?= $item['descr']; ?></label>
                                <input type="text" name="<?= $item['name']; ?>" value="<?= $item['value']; ?>" />
                            </p>
                        </li>
                    <? endforeach; ?>
                    <input type="submit" value="Обновить">
                </ul>
                <? endif; ?>
            </form>

        </div>
    </aside>
</div>