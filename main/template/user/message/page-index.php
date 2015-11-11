<?php
/**
 * @var $page   \View\User\Address\IndexPage
 * @var $helper \Helper\TemplateHelper
 */
?>

<div class="personal">
    <?= $page->render('user/_menu-1508', ['page' => $page]) ?>

    <div class="personalPage">

        <div class="private-sections private-sections_gray grid js-messageContainer">
            <h1 class="private-sections__head">Сообщения</h1>

            <div class="management-message">
                <form action="#" name="message">
                    <fieldset class="management-message__item">
                        <input class="management-message__checkbox customInput js-MessageAll" id="selectAll" type="checkbox">
                        <label class="management-message__select-all label-for-customInput " for="selectAll">
                            Выбрать все
                        </label>
                    </fieldset>

                    <input class="management-message__item management-message__item_btn js-messageRead" type="submit" value="Отметить как прочитанное">
                    <input class="management-message__item management-message__item_btn js-messageRemove" type="submit" value="Удалить">
                </form>
            </div>
            <div class="grid__col">

                <ul class="message-list">
                    <li class="message-list__item message-list__item_new-center js-message">
                        <a class="message-list__link clearfix" href="#" target="_blank">
                                <fieldset class="message-list__checkbox">
                                    <input class="customInput js-messageCheckbox" id="message-1" type="checkbox" form="message">
                                    <label class="label-for-customInput " for="message-1">
                                    </label>
                                </fieldset>

                            <div class="message-list__left message-list__left_big">

                                <div class="message-list__title">
                                    Заказ #COXD-123456 накаав дылваолдыва
                                </div>

                                <p class="message-list__text">
                                    Ваш заказ передан в транспортную компанию! Ваш Enter! приятных гадин
                                </p>
                            </div>

                            <div class="message-list__date">
                                <span>17.08.2015</span>
                                <span>11:10</span>
                            </div>
                        </a>
                    </li>

                    <li class="message-list__item message-list__item_new-center js-message">
                        <a class="message-list__link clearfix" href="#" target="_blank">
                            <fieldset class="message-list__checkbox">
                                <input class="customInput js-messageCheckbox" id="message-2" type="checkbox" form="message">
                                <label class="label-for-customInput" for="message-2">
                                </label>
                            </fieldset>

                            <div class="message-list__left message-list__left_big">

                                <div class="message-list__title">
                                    Заказ #COXD-123456 накаав дылваолдыва
                                </div>

                                <p class="message-list__text">
                                    Ваш заказ передан в транспортную компанию! Ваш Enter! приятных гадин
                                </p>
                            </div>

                            <div class="message-list__date">
                                <span>17.08.2015</span>
                                <span>11:10</span>
                            </div>
                        </a>
                    </li>

                    <li class="message-list__item js-message">
                        <a class="message-list__link clearfix" href="#" target="_blank">
                            <fieldset class="message-list__checkbox">
                                <input class="customInput js-messageCheckbox" id="message-3" type="checkbox" form="message">
                                <label class="label-for-customInput" for="message-3">
                                </label>
                            </fieldset>

                            <div class="message-list__left message-list__left_big">

                                <div class="message-list__title">
                                    Заказ #COXD-123456 накаав дылваолдыва
                                </div>

                                <p class="message-list__text">
                                    Ваш заказ передан в транспортную компанию! Ваш Enter! приятных гадин
                                </p>
                            </div>

                            <div class="message-list__date">
                                <span>17.08.2015</span>
                                <span>11:10</span>
                            </div>
                        </a>
                    </li>

                </ul>


                <!--#####Вывести если сообщения отсутствуют

                    <div class="item-none item-none_statis">
                        <div class="item-none__img-block">
                            <img src="/styles/personal-page/img/no-message.png" alt="#">
                        </div>
                        <span class="item-none__txt">
                            У вас еще нет сообщений
                        </span>
                    </div>-->
            </div>

        </div>

        <div class="private-sections__modal js-modalLk">
            <article class="private-sections__modal-body private-sections__modal-body_small">
                <header class="private-sections__modal-head">
                    Удалить адрес?
                </header>
                <div class="js-copyContentIn">

                </div>
                <button class="address-list__item-del_big js-btnContainerDel js-modal-close">Удалить</button>
                <a class="private-sections__modal-close js-modal-close" href="#"></a>
            </article>
        </div>

    </div>
</div>
