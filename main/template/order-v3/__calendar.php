<?php

return function(
    \Helper\TemplateHelper $helper,
    $id,
    array $possible_days
) {

    $lastAvailableDay = DateTime::createFromFormat('U', (string)end($possible_days));
    $firstAvailableDay = DateTime::createFromFormat('U', (string)reset($possible_days));
    $firstDayOfAvailableWeek = DateTime::createFromFormat('U', strtotime('this week', $firstAvailableDay->format('U')));
    $lastDayOfAvailableMonth = DateTime::createFromFormat('U', strtotime('Monday next week', $lastAvailableDay->format('U')));
//    $lastDayOfAvailableMonth->modify('+1 day');
    $calendar = new DatePeriod($firstDayOfAvailableWeek, new DateInterval('P1D'), $lastDayOfAvailableMonth);

    $currMonth = null;

?>

    <div class="celedr popupFl" style="display: none" id="<?= $id ?>">
        <div class="popupFl_clsr jsCloseFl"></div>

        <div class="celedr_t"><?= mb_strtolower(\Util\Date::strftimeRu('%e %B2, %A', time()))?></div>

        <button class="celedr_btn btn2">Хочу быстрее!</button>

        <div class="celedr_tb">
            <div class="celedr_row celedr_row-h clearfix">
                <div class="celedr_col">Пн</div>
                <div class="celedr_col">Вт</div>
                <div class="celedr_col">Ср</div>
                <div class="celedr_col">Чт</div>
                <div class="celedr_col">Пт</div>
                <div class="celedr_col">Сб</div>
                <div class="celedr_col">Вс</div>
            </div>

            <div class="celedr_row clearfix">

                <div class="celedr_month"><?= strftime('%B', $firstDayOfAvailableWeek->format('U')) ?></div>
                <? $currMonth = $firstDayOfAvailableWeek->format('F') ?>

                <? foreach ($calendar as $day) : ?>
                    <? /** @var $day DateTime */ ?>

                    <? if ($currMonth != $day->format('F')) : ?>

                        <? $isMonday = $day->format('N') == 1 ?>

                        <? if (!$isMonday) : ?>
                            <? for ($i = 0; $i < 8 - $day->format('N'); $i++) : ?>
                                <div class="celedr_col celedr_col-disbl"></div>
                            <? endfor; ?>
                        <? endif; ?>

                        <div class="celedr_month"><?= strftime('%B', $day->format('U')) ?></div>
                        <? $currMonth = $day->format('F') ?>

                        <? if (!$isMonday) : ?>
                            <? for ($i = 1; $i < $day->format('N'); $i++) : ?>
                                <div class="celedr_col celedr_col-disbl"></div>
                            <? endfor; ?>
                        <? endif; ?>

                    <? endif; ?>

                    <? $isDayAvailable = in_array((int)$day->format('U'), $possible_days) ?>
                    <div class="celedr_col <?= $isDayAvailable ? '' : 'celedr_col-disbl' ?>" data-value="<?= $isDayAvailable ? $day->format('U') : '' ?>"><?= $day->format('d')?></div>

                <? endforeach; ?>

            </div>
        </div>
    </div>

<? };