<?
/**
 * @var $version int
 */
$style = $version == 1 ? "margin: 0 0 0px 20px; color: #6E6E6E" : "margin: 0 0 10px 2px; color: #6E6E6E";
$time = time();
// временной промежуток для показа
$isActiveTime = $time < 1417392000;

if ($isActiveTime) :
?>

<div style="<?= $style ?>">
    В период действия BLACK FRIDAY возможны задержки доставки на 1-2 дня.<br/>
    Мы обязательно позвоним Вам или отправим СМС.
</div>

<? endif; ?>