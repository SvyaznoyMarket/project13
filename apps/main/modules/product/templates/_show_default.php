<div class="goodsphoto"><i class="bestseller"></i><a href=""><img src="/images/images/photo25.jpg" alt="" width="500" height="500" title="" /></a></div>
<div class="goodsinfo"><!-- Goods info -->
        <div class="article">
            <div class="fr"><a href="">Следить за товаром</a> <a href="" rel="nofollow">Печать</a></div>
            Артикул #<?php echo $item['product']->article ?>

            <!-- Watch -->
            <div class="hideblock width358">
                <i title="Закрыть" class="close">Закрыть</i>
                <div class="title">Получать сообщения</div>
                <form action="" class="form">
                    <ul class="checkboxlist pb10">
                        <li><label for="checkbox-7">когда снизится цена</label><input id="checkbox-7" name="checkbox-3" type="checkbox" value="checkbox-1" /></li>
                        <li><label for="checkbox-8">когда появится новый отзыв </label><input id="checkbox-8" name="checkbox-3" type="checkbox" value="checkbox-2" /></li>
                        <li><label for="checkbox-9">когда товар появится в магазинах сети</label><input id="checkbox-9" name="checkbox-3" type="checkbox" value="checkbox-3" /></li>
                    </ul>
                    <div class="pb5">Ваш E-mail:</div>
                    <input type="text" class="text width181 mb10" value="user@mail.ru" />
                    <div class="pb20"><input type="button" class="yellowbutton yellowbutton106" value="Подтверждаю" /></div>
                    <div class="font11 gray">Внимание!<br />Вы всегда сможете отписаться от данной рассылки в самой рассылкеили в личном кабинете</div>
                </form>
            </div>
            <!-- /Watch -->

        </div>
        <div class="font14 pb15"><?php echo $item['product']->tagline ?></div>
        <div class="clear"></div>

        <div class="fl pb15">
            <div class="font10">Старая цена<br /><span class="through">33 990 <span class="rubl">&#8399;</span></span></div>
            <div class="pb10"><?php include_partial('product/price', array('price' => $item['price'], )) ?></div>
            <div class="pb5"><strong class="orange">Есть в наличии</strong></div>
            <div class="pb3"><strong>Доставка стандарт</strong></div>
            <div class="font11 gray">
                Стоимость: <strong>350 руб.</strong><br />
                Москва. Доставим в течение 1-2 дней<br />
                <a href="" class="underline">Хотите быстрее?</a>
            </div>
        </div>
        <div class="fr ar pb15">
            <div class="doodsbarbig">
                <?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?>
                <!--a href="" class="link1"></a-->
                <?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?>
                <?php include_component('userProductCompare', 'button', array('product' => $product)) ?>
            </div>
            <div class="pb5"><strong><a href="" class="red underline">Купить быстро в 1 клик</a></strong></div>
            <a href="" class="underline">Где купить в магазинах?</a>
        </div>

        <div class="line pb15"></div>

        <div class="pb5"><a href="" class="underline">Читать отзывы</a> (25)</div>
        <div class="pb5"><?php include_component('userProductRating', 'show', array('product' => $product)) ?></div>
        <div class="pb5">Понравилось? <a href="" class="share">Поделиться</a> <strong><a href="" class="nodecor">+87</a></strong></div>
        <div class="pb3"><?php include_component('userTag', 'product_link', array('product' => $product)) ?></div>

        <div class="f1links form">
            <div class="f1linkbox">
                <a href="" class="f1link">Сервис F1</a> Сервис F1
            </div>
            <div class="f1linkslist">
                <ul>
                    <li><label for="checkbox-1">Установка кресел и диванов (1990 Р)</label><input id="checkbox-1" name="checkbox-1" type="checkbox" value="checkbox-1" /></li>
                    <li><label for="checkbox-2">Чистка кресел и диванов  (690 Р)</label><input id="checkbox-2" name="checkbox-1" type="checkbox" value="checkbox-2" /></li>
                    <li><label for="checkbox-3">Ремонт и восстановление кресел и диванов  (2990 Р)</label><input id="checkbox-3" name="checkbox-1" type="checkbox" value="checkbox-3" /></li>
                </ul>
                <a href="" class="underline">подробнее</a>
            </div>
        </div>

        <div class="line pb15"></div>

<?php echo $product->Creator ?>
<?php include_component('product', 'product_group', array('product' => $product, )) ?>
<ul class="inline">
  <li><?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?></li>
  <li><?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?></li>
  <li><?php include_component('userProductCompare', 'button', array('product' => $product)) ?></li>
</ul>

<!--div class="inline">
  <?php //include_component('userProductRating', 'show', array('product' => $product)) ?>
</div->

<div class="inline">
  <?php //include_component('userTag', 'product_link', array('product' => $product)) ?>
</div>

<div class="block">
  <?php //echo link_to('Следить за этим товаром', 'userProductNotice_show', $sf_data->getRaw('product'), array('class' => 'event-click', 'data-event' => 'window.open')) ?>
</div-->

</div><!-- Goods info -->

    <div class="clear"></div>

    <!-- Description -->
    <h2 class="bold">Характеристики</h2>
    <div class="line pb25"></div>

    <div class="descriptionlist">
    <?php include_component('product', 'property_grouped', array('product' => $product)) ?>
    </div>

    <div class="descriptionlist" style="display:none">
        <div class="point">
            <div class="title"><h3>Процессор</h3></div>
            <div class="description">
                AMD Phenom II Triple-Core Mobile P820 (1.8 ГГц, 800 Mгц, 2Мб кэш)
                <div>Отличная производительность, экономия заряда батареи</div>
            </div>
        </div>
        <div class="point">
            <div class="title"><h3>Оперативная память</h3></div>
            <div class="description">
                3 Гб
                <div>Быстрый запуск приложений</div>
            </div>
        </div>
        <div class="point">
            <div class="title"><h3>HDD</h3></div>
            <div class="description">
                320 Гб
                <div>Около 80.000 песен или 300.000 фотографий</div>
            </div>
        </div>
        <div class="point">
            <div class="title"><h3>Операционная система</h3></div>
            <div class="description">
                Windows 7 “Домашняя расширенная”
                <div>Лучшее решение для просмотра фильмов, фотографий и музыки</div>
            </div>
        </div>
        <div class="point">
            <div class="title"><h3>Батарея</h3></div>
            <div class="description">
                8 часов
                <div>Около 80.000 песен или 300.000 фотографий</div>
            </div>
        </div>

        <div style="display:none"></div>
    </div>

    <div class="pb25"><a href="" class="more">Все характеристики</a></div>
    <!-- /Description -->