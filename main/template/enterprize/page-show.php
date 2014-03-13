<?php
/**
 * @var $page            \View\DefaultLayout
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity
 */
?>

<a class="formDefault__btnSubmit getCuponEP mBtnOrange" href="<?= $page->url('enterprize.form.show', ['enterprizeToken' => $enterpizeCoupon->getToken()]) ?>">Получить</a>