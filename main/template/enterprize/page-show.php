<?php
/**
 * @var $page            \View\DefaultLayout
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity
 * @var $limit           integer|null
 */
?>

<? if (is_numeric($limit) && 0 === $limit): ?>
    <p>К сожалению, эти фишки закончились. <a href="<?= $page->url('enterprize') ?>">Посмотреть другие фишки</a></p>
<? else: ?>
    <a class="formDefault__btnSubmit getCuponEP mBtnOrange" href="<?= $page->url('enterprize.form.show', ['enterprizeToken' => $enterpizeCoupon->getToken()]) ?>">Получить</a>
<? endif ?>