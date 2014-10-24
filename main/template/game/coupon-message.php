<?
$helper = new \Helper\TemplateHelper();
$priceNumDecimals = false === strpos((string)$value, '.') ? 0 : 2;
?>

<?= (isset($errorMessage) ? $errorMessage . '<br/>' : null) ?>
Фишка со скидкой <?= $helper->formatPrice($value, $priceNumDecimals) ?><?= $is_currency ? 'руб' : '%' ?> на <a href="<?=$url?>">"<?= $label ?>"</a><br/>
Минимальная сумма заказа <?=$minOrder?>руб<br/>
Действует c <?=$startDate?> по <?=$endDate?><br/>
Поделиться радостью с друзьями:
<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<div class="yashare-auto-init pc_buttons" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,moimir"></div>