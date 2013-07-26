<?php
/**
 * @var $page               \View\Order\CreatePage
 * @var $user               \Session\User
 * @var $orders             \Model\Order\Entity[]
 * @var $isOrderAnalytics   bool
 */
?>

<?php
$isOrderAnalytics = isset($isOrderAnalytics) ? $isOrderAnalytics : true;
?>

<!-- Header -->
<div class='bBuyingHead'>
    <a href="<?= $page->url('homepage') ?>"></a>
    <i></i><br>
    <span>Ваш заказ не оплачен</span>
</div>
<!-- /Header -->

<p class="title-font16 font16">
  Произошла ошибка или Вы решили не оплачивать заказ выбранным способом ?<br>
  Если Вы все еще желаете получить заказ, свяжитесь с оператором контакт-сEnter по номеру<br>
  8 (800) 700-00-09!
</p>

<div class="line pb15"></div>

<div class="mt32 clearfix socnet-ico-box">
    <ul class="socnet-ico-list">
        <li class="socnet-ico-list__yam"><a target="_blank" class="socnet-ico-list-link" data-type="Yandex Market" href="http://www.enter.ru/market" ></a></li>
        <li class="socnet-ico-list__fs"><a target="_blank" class="socnet-ico-list-link" data-type="Foursquare" href="http://ru.foursquare.com/enter_ru"></a></li>
        <li class="socnet-ico-list__inst"><a target="_blank" class="socnet-ico-list-link" data-type="Instagram" href="http://instagram.com/enterllc"></a></li>
        <li class="socnet-ico-list__yt"><a target="_blank" class="socnet-ico-list-link" data-type="YouTube" href="http://www.youtube.com/user/EnterLLC"></a></li>
        <li class="socnet-ico-list__vk"><a target="_blank" class="socnet-ico-list-link" data-type="Vkontakte" href="http://vk.com/youcanenter"></a></li>
        <li class="socnet-ico-list__tw"><a target="_blank" class="socnet-ico-list-link" data-type="Twitter" href="http://twitter.com/enter_ru"></a></li>
        <li class="socnet-ico-list__fb"><a target="_blank" class="socnet-ico-list-link" data-type="FaceBook" href="http://www.facebook.com/enter.ru"></a></li>
    </ul>
</div>

<div class="mt32" style="text-align: center">
    <a class='bBigOrangeButton' href="<?= $page->url('homepage') ?>">Продолжить покупки</a>
</div>
