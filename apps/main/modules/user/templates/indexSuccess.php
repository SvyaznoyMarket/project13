<?php if (false): ?>
<div class="block">
  <ul>
    <li><?php echo link_to('Корзина товаров', 'cart', array(), array('class' => 'cart')) ?></li>
    <li><?php echo link_to('Адреса доставки', 'userAddress') ?></li>
    <li><?php echo link_to('Пароль', 'user_changePassword') ?></li>
    <li><?php echo link_to('Выход', 'user_signout') ?></li>
  </ul>
</div>
<?php endif ?>
<?php slot('title','Личный кабинет') ?>

<?php slot('navigation') ?>
  <?php include_component('user', 'navigation') ?>
<?php end_slot() ?>

<div class="float100">
    <div class="column685 ">
        <div class="fl width315">

            <?php foreach ($pagesList as $part): ?>
                <div class="font16 orange pb10"><?php echo $part['title']?></div>
                <ul class="leftmenu pb20">
                <?php foreach ($part['list'] as $item): ?>
                    <li<?php echo (isset($item['current']) && $item['current'])  ? ' class="current"' : '' ?>>
                        <a href="<?php echo url_for($item['url']) ?>">
                            <?php echo $item['name'] ?>
                            <?php if (isset($item['num'])) echo '('.$item['num'].')'?>
                        </a>
                    </li>
                <?php endforeach ?>
                </ul>
            <?php endforeach ?>

        </div>
    </div>
</div>

<div class="column215">
    <?php  include_component('user', 'shortuserinfo') ?>
    <div class="cabinethelp">Заполни о себе больше информации это позволит нам сделать тебе интересные предложения </div>
    <div class="pb15"><form action="<?php echo url_for('user_edit') ?>" method="get"><input type="submit" class="button whitebutton" id="whitebutton" value="Заполнить мои данные" /></form></div>
    <?php if (false): ?>
    <div class="line pb15"></div>
    <div class="cabinethelp">Заполни свой постоянный адрес доставки, чтобы при повторных заказах не пришлось терять время при заполнении полей</div>
    <div class="pb15"><input type="button" class="button whitebutton" id="whitebutton" value="Заполнить мой адрес доставки" /></div>
    <div class="line pb15"></div>
    <div class="cabinethelp">Вы зарегестрировали как юридическое лицо, рекомендуем вам заполнить форму реквизитов вашей компании</div>
    <div><input type="button" class="button whitebutton" id="whitebutton" value="Заполнить реквизиты компании" /></div>
    <?php endif ?>
</div>