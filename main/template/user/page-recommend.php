<?php
/**
 * @var $page                \View\User\OrderPage
 * @var $user                \Session\User
 * @var $recommendedProducts \Model\Product\Entity[]
 * @var $viewedProducts      \Model\Product\Entity[]
 */
?>

<?
$helper = new \Helper\TemplateHelper();
$isNewProductPage = \App::abTest()->isNewProductPage();
?>

<div class="personalPage personal">

    <div class="personal__menu">
        <ul class="personal-navs">
            <li class="personal-navs__i">
                <a href="#" class="personal-navs__lk">Наше все</a>
            </li>
            <li class="personal-navs__i">
                <a href="#" class="personal-navs__lk">Заказы</a>
            </li>
            <li class="personal-navs__i">
                <a href="#" class="personal-navs__lk">Избранное</a>
            </li>
            <li class="personal-navs__i">
                <a href="#" class="personal-navs__lk">Подписки</a>
            </li>
            <li class="personal-navs__i active">
                <a href="#" class="personal-navs__lk">Личные данные</a>
            </li>
            <li class="personal-navs__i right-nav">
                <a href="#" class="personal-navs__lk">Адвокат клиента</a>
            </li>
        </ul>
    </div>

    <div class="personal__password">
        <div class="personal__sub-head">Изменить пароль</div>
        <p>Надежный пароль должен содержать от 6 до 16 знаков следующих трех видов: прописные буквы, строчные буквы, цифры или символы, но не должен включать широко распространенные слова и имена.</p>
        <form>
            <div class="form-group">
                <label class="label-control">Старый пароль</label>
                <input class="input-control" type="password">
            </div>
            <div class="form-group">
                <label class="label-control">Новый пароль</label>
                <input class="input-control" type="password">
            </div>
            <div class="form-group">
                <label class="label-control">Повторите пароль</label>
                <input class="input-control" type="password">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-type btn-type--buy">Сохранить</button>
            </div>
        </form>
    </div>

    <div class="personal__info">
        <form>
            <div class="form-group">
                <label class="label-control">Имя</label>
                <input class="input-control" type="text">
            </div>
            <div class="form-group">
                <label class="label-control">Отчество</label>
                <input class="input-control" type="text">
            </div>
            <div class="form-group">
                <label class="label-control">Фамилия</label>
                <input class="input-control" type="text">
            </div>
            <div class="form-group inline">
                <label class="label-control">Дата рождения</label>
                <div class="custom-select custom-select--day">
                    <select class="custom-select__inn">
                        <option class="custom-select__i">1</option>
                        <option class="custom-select__i">2</option>
                        <option class="custom-select__i">3</option>
                        <option class="custom-select__i">4</option>
                    </select>
                </div>
                <div class="custom-select custom-select--month">
                    <select class="custom-select__inn">
                        <option class="custom-select__i">1</option>
                        <option class="custom-select__i">2</option>
                        <option class="custom-select__i">3</option>
                        <option class="custom-select__i">4</option>
                        <option class="custom-select__i">5</option>
                        <option class="custom-select__i">6</option>
                        <option class="custom-select__i">7</option>
                        <option class="custom-select__i">8</option>
                        <option class="custom-select__i">9</option>
                        <option class="custom-select__i">10</option>
                        <option class="custom-select__i">11</option>
                        <option class="custom-select__i">12</option>
                    </select>
                </div>
                <div class="custom-select custom-select--year">
                    <select class="custom-select__inn">
                        <option class="custom-select__i">1999</option>
                        <option class="custom-select__i">2000</option>
                        <option class="custom-select__i">2001</option>
                        <option class="custom-select__i">2002</option>
                    </select>
                </div>
            </div>
            <div class="form-group inline right">
                <label class="label-control">Пол</label>
                <div class="custom-select custom-select--sex">
                    <select class="custom-select__inn">
                        <option class="custom-select__i">Мужской</option>
                        <option class="custom-select__i">Женский</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="label-control">email не редактируется</label>
                <input class="input-control disabled" type="text" disabled value="email@email.com">
            </div>
            <div class="form-group">
                <label class="label-control">Мобильный телефон</label>
                <input class="input-control" type="text">
            </div>
            <div class="form-group">
                <label class="label-control">Домашний телефон</label>
                <input class="input-control" type="text">
            </div>
            <div class="form-group">
                <label class="label-control">Род деятельности</label>
                <input class="input-control" type="text">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-type btn-type--buy">Сохранить изменения</button>
            </div>
        </form>
    </div>
    <div class="personalPage personal">

        <div class="personal__menu">
            <ul class="personal-navs">
                <li class="personal-navs__i">
                    <a href="#" class="personal-navs__lk">Наше все</a>
                </li>
                <li class="personal-navs__i">
                    <a href="#" class="personal-navs__lk">Заказы</a>
                </li>
                <li class="personal-navs__i">
                    <a href="#" class="personal-navs__lk">Избранное</a>
                </li>
                <li class="personal-navs__i">
                    <a href="#" class="personal-navs__lk">Подписки</a>
                </li>
                <li class="personal-navs__i active">
                    <a href="#" class="personal-navs__lk">Личные данные</a>
                </li>
                <li class="personal-navs__i right-nav">
                    <a href="#" class="personal-navs__lk">Адвокат клиента</a>
                </li>
            </ul>
        </div>

        <div class="personal__orders current">
            <div class="personal-order__item">
                <div class="personal-order__cell">
                    <span class="personal-order__num">COXF-767608</span>
                    <span class="personal-order__date">01.01.2015</span>
                </div>
                <div class="personal-order__cell">
                    <div class="personal-order__name ellipsis">Сетевой фильтр ЭРА 5гн+2xUSB, 2м, SFU-5es-2m-W sdfafsdkfjga sfakjfgafassjgas fkjag</div>
                    <span class="personal-order__info warning">Требуется предоплата</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__deliv-type">Самовывоз 18.06.2015</span>
                    <div class="personal-order__deliv-info ellipsis">Постамат PickPoint<br>ул. Братиславская д. 14 sdlfkjahfasldkjahsdalskjhljksag lkgasdl lajdg sldjg</div>
                </div>
                <div class="personal-order__cell personal-order__price">
                    550 <span class="rubl">p</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__status">Подтвержден</span>
                    <span class="personal-order__pay-status online">Оплатить онлайн</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__more">Еще
                        <div class="personal-order__cancel">Отменить заказ</div>
                    </span>

                </div>
            </div>
        </div>
        <div class="personal__orders">
            <div class="personal-order__block">
                <span class="personal-order__year-container">
                   <span class="personal-order__year"> 2015</span>
                </span><span class="personal-order__year-total">5 заказов</span>
            </div>
            <div class="personal-order__item">
                <div class="personal-order__cell">
                    <span class="personal-order__num">COXF-767608</span>
                    <span class="personal-order__date">01.01.2015</span>
                </div>
                <div class="personal-order__cell">
                    <div class="personal-order__name ellipsis">Сетевой фильтр ЭРА 5гн+2xUSB, 2м, SFU-5es-2m-W sdfafsdkfjga sfakjfgafassjgas fkjag</div>
                    <span class="personal-order__info warning">Требуется предоплата</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__deliv-type">Самовывоз 18.06.2015</span>
                    <div class="personal-order__deliv-info ellipsis">Постамат PickPoint<br>ул. Братиславская д. 14 sdlfkjahfasldkjahsdalskjhljksag lkgasdl lajdg sldjg</div>
                </div>
                <div class="personal-order__cell personal-order__price">
                    550 <span class="rubl">p</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__status">Подтвержден</span>
                    <span class="personal-order__pay-status online">Оплатить онлайн</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__more">Еще
                        <div class="personal-order__cancel">Отменить заказ</div>
                    </span>

                </div>
            </div>
        </div>
    </div>


    <?= $page->render('user/_menu', ['page' => $page]) ?>

    <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
        'type'           => 'main',
        'title'          => 'Мы рекомендуем',
        'products'       => $recommendedProducts,
        'count'          => count($recommendedProducts),
        'limit'          => \App::config()->product['itemsInSlider'],
        'page'           => 1,
        'class'          => $isNewProductPage ? '' : 'slideItem-7item',
        'sender'   => [
            'name'     => 'retailrocket',
            'position' => 'UserRecommended',
            'method'   => 'PersonalRecommendation',
        ],
    ]) ?>

    <?= $helper->render('product/__slider', [
        'type'      => 'viewed',
        'title'     => 'Вы смотрели',
        'products'  => $viewedProducts,
        'count'     => count($viewedProducts),
        'limit'     => \App::config()->product['itemsInSlider'],
        'page'      => 1,
        'class'     => 'slideItem-viewed',
        'isCompact' => true,
    ]) ?>

</div>