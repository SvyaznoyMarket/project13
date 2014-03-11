<?php
/**
 * @var $page            \View\DefaultLayout
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity
 */
?>

<a style="margin: -15px 0 30px 10px; width: 100px;" class="formDefault__btnSubmit mBtnOrange" href="<?= $page->url('enterprize.form.show', ['enterprizeToken' => $enterpizeCoupon->getToken()]) ?>">Получить</a>