<?php
/**
 * @var $page       \Templating\HtmlLayout
 * @var $user       \Session\User
 * @var $url        string
 * @var $url_params array|null
 */

?>
<? if ($url_params == null) : ?>
    <form class="form jsPaymentForms jsPaymentFormPaypal" method="get" action="<?= $url ?>">
    </form>
<? else : ?>
    <form class="form jsPaymentForms" method="get" action="<?= $url ?>">
        <? foreach ($url_params as $key => $val) : ?>
            <input type="hidden" name="<?= $key ?>" value="<?= $val ?>" />
        <? endforeach; ?>
    </form>
<? endif; ?>