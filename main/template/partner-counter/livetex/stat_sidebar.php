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
            <form action="/livetex-statistics">
                <ul>
                    <li>
                        <label for="date_begin">Дата начала</label>
                        <input type="text" name="date_begin" value="<?= ($date_begin); ?>" />
                    </li>

                    <li>
                        <label for="date_begin">Дата окончания</label>
                        <input type="text" name="date_end" value="<?= ($date_end); ?>" />
                    </li>
                    <input type="submit" value="Отправить">
                </ul>
            </form>

        </div>
    </aside>
</div>