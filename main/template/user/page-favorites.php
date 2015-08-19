<?php
/**
 * @var $page                 \View\User\OrdersPage
 * @var $helper               \Helper\TemplateHelper
 * @var $user                 \Session\User
 * @var $products             \Model\Product\Entity[]
 * @var $favoriteProductsByUi \Model\Favorite\Product\Entity[]
 */
?>

<div class="personal">
    <?= $page->render('user/_menu', ['page' => $page]) ?>

    <!-- страница избранное -->
        <div class="personal__favorits">
            <div class="personal-favorit__top">
                <div class="personal-favorit__choose">
                    <input id="cb1" type="checkbox" class="personal-favorit__checkbox js-fav-all">
                    <label for="cb1" class="personal-favorit__checkbox-icon"></label>
                    <span class="choose-all">Выбрать все</span>
                </div>
                <ul class="personal-favorit__acts">
                    <li class="personal-favorit__act js-fav-popup-show" data-popup="js-share-popup">Поделиться</li>
                    <li class="personal-favorit__act js-fav-popup-show" data-popup="js-create-popup">Создать список</li>
                    <li class="personal-favorit__act js-fav-popup-show" data-popup="js-move-popup">Перенести в список</li>
                    <li class="personal-favorit__act js-fav-popup-show" data-popup="js-del-popup">Удалить</li>
                </ul>
            </div>
            <div class="personal-favorit__item">
                <div class="personal-favorit__cell personal-favorit__choose">
                    <input class="personal-favorit__checkbox" type="checkbox" id="cb2">
                    <label for="cb2" class="personal-favorit__checkbox-icon"></label>
                </div>
                <div class="personal-favorit__cell personal-favorit__pic">
                    <img src="http://2.imgenter.ru/uploads/media/c9/2b/5f/thumb_85b6_product_60.jpeg">
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__name">Бумажный конструктор Jazwares Minecraft Papercraft «Дружелюбные мобы»</div>
                    <div class="personal-favorit__status">В наличии</div>
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__price"><span class="old-price"><span class="old-price__stroke">1000</span> <span class="rubl">p</span></span>750 <span class="rubl">p</span></div>
                    <div class="personal-favorit__buy">
                        <button type="submit" class="btn-type btn-type--buy">Купить</button>
                    </div>
                    <div class="personal-favorit__reminds">
                        <span class="remind-text">Сообщить</span>
                        <div class="personal-favorit__price-change">
                            <div class="personal__hint">о снижении цены</div>
                        </div>
                        <div class="personal-favorit__stock">
                            <div class="personal__hint">о наличии</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="personal-favorit__item">
                <div class="personal-favorit__cell personal-favorit__choose">
                    <input class="personal-favorit__checkbox" type="checkbox" id="cb3">
                    <label for="cb3" class="personal-favorit__checkbox-icon"></label>
                </div>
                <div class="personal-favorit__cell personal-favorit__pic">
                    <img src="http://0.imgenter.ru/uploads/media/7f/fb/28/thumb_dd20_product_120.jpeg">
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__name">Бумажный конструктор Jazwares Minecraft Papercraft «Дружелюбные мобы»</div>
                    <div class="personal-favorit__status unavailable">Нет в наличии</div>
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__price">700 <span class="rubl">p</span></div>
                    <div class="personal-favorit__buy">
                        <button type="submit" class="btn-type btn-type--buy">Купить</button>
                    </div>
                    <div class="personal-favorit__reminds">
                        <span class="remind-text">Сообщить</span>
                        <div class="personal-favorit__price-change">
                            <div class="personal__hint">о снижении цены</div>
                        </div>
                        <div class="personal-favorit__stock">
                            <div class="personal__hint">о наличии</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="personal__favorits favorit-list expanded">
            <div class="favorit-list__header">
                <ul class="personal-favorit__acts">
                    <li class="personal-favorit__act js-fav-popup-show" data-popup="js-share-goods-popup">Поделиться</li>
                    <li class="personal-favorit__act js-fav-popup-show" data-popup="js-create-popup">Создать список</li>
                </ul>
                <div class="favorit-list__name">Список: на ДР</div>
            </div>
            <div class="personal-favorit__top">
                <div class="personal-favorit__choose">
                    <input id="cb4" type="checkbox" class="personal-favorit__checkbox">
                    <label for="cb4" class="personal-favorit__checkbox-icon"></label>
                    <span class="choose-all">Выбрать все</span>
                </div>
            </div>
            <div class="personal-favorit__item">
                <div class="personal-favorit__cell personal-favorit__choose">
                    <input class="personal-favorit__checkbox" type="checkbox" id="cb5">
                    <label for="cb5" class="personal-favorit__checkbox-icon"></label>
                </div>
                <div class="personal-favorit__cell personal-favorit__pic">
                    <img src="http://2.imgenter.ru/uploads/media/c9/2b/5f/thumb_85b6_product_60.jpeg">
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__name">Бумажный конструктор Jazwares Minecraft Papercraft «Дружелюбные мобы»</div>
                    <div class="personal-favorit__status">В наличии</div>
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__price"><span class="old-price"><span class="old-price__stroke">1000</span> <span class="rubl">p</span></span>750 <span class="rubl">p</span></div>
                    <div class="personal-favorit__buy">
                        <button type="submit" class="btn-type btn-type--buy">Купить</button>
                    </div>
                    <div class="personal-favorit__reminds">
                        <span class="remind-text">Сообщить</span>
                        <div class="personal-favorit__price-change">
                            <div class="personal__hint">о снижении цены</div>
                        </div>
                        <div class="personal-favorit__stock">
                            <div class="personal__hint">о наличии</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="personal-favorit__item">
                <div class="personal-favorit__cell personal-favorit__choose">
                    <input class="personal-favorit__checkbox" type="checkbox" id="cb6">
                    <label for="cb6" class="personal-favorit__checkbox-icon"></label>
                </div>
                <div class="personal-favorit__cell personal-favorit__pic">
                    <img src="http://0.imgenter.ru/uploads/media/7f/fb/28/thumb_dd20_product_120.jpeg">
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__name">Бумажный конструктор Jazwares Minecraft Papercraft «Дружелюбные мобы»</div>
                    <div class="personal-favorit__status unavailable">Нет в наличии</div>
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__price">700 <span class="rubl">p</span></div>
                    <div class="personal-favorit__buy">
                        <button type="submit" class="btn-type btn-type--buy">Купить</button>
                    </div>
                    <div class="personal-favorit__reminds">
                        <span class="remind-text">Сообщить</span>
                        <div class="personal-favorit__price-change">
                            <div class="personal__hint">о снижении цены</div>
                        </div>
                        <div class="personal-favorit__stock">
                            <div class="personal__hint">о наличии</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="personal__favorits favorit-list">
            <div class="favorit-list__header">
                <ul class="personal-favorit__acts">
                    <li class="personal-favorit__act js-fav-popup-show" data-popup="js-share-goods-popup">Поделиться</li>
                    <li class="personal-favorit__act js-fav-popup-show" data-popup="js-create-popup">Создать список</li>
                </ul>
                <div class="favorit-list__name">Список: на ДР</div>
            </div>
            <div class="personal-favorit__top">
                <div class="personal-favorit__choose">
                    <input id="cb1" type="checkbox" class="personal-favorit__checkbox">
                    <label for="cb1" class="personal-favorit__checkbox-icon"></label>
                    <span class="choose-all">Выбрать все</span>
                </div>
            </div>
            <div class="personal-favorit__item">
                <div class="personal-favorit__cell personal-favorit__choose">
                    <input class="personal-favorit__checkbox" type="checkbox" id="cb2">
                    <label for="cb2" class="personal-favorit__checkbox-icon"></label>
                </div>
                <div class="personal-favorit__cell personal-favorit__pic">
                    <img src="http://2.imgenter.ru/uploads/media/c9/2b/5f/thumb_85b6_product_60.jpeg">
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__name">Бумажный конструктор Jazwares Minecraft Papercraft «Дружелюбные мобы»</div>
                    <div class="personal-favorit__status">В наличии</div>
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__price"><span class="old-price"><span class="old-price__stroke">1000</span> <span class="rubl">p</span></span>750 <span class="rubl">p</span></div>
                    <div class="personal-favorit__buy">
                        <button type="submit" class="btn-type btn-type--buy">Купить</button>
                    </div>
                    <div class="personal-favorit__reminds">
                        <span class="remind-text">Сообщить</span>
                        <div class="personal-favorit__price-change">
                            <div class="personal__hint">о снижении цены</div>
                        </div>
                        <div class="personal-favorit__stock">
                            <div class="personal__hint">о наличии</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="personal-favorit__item">
                <div class="personal-favorit__cell personal-favorit__choose">
                    <input class="personal-favorit__checkbox" type="checkbox" id="cb7">
                    <label for="cb7" class="personal-favorit__checkbox-icon"></label>
                </div>
                <div class="personal-favorit__cell personal-favorit__pic">
                    <img src="http://0.imgenter.ru/uploads/media/7f/fb/28/thumb_dd20_product_120.jpeg">
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__name">Бумажный конструктор Jazwares Minecraft Papercraft «Дружелюбные мобы»</div>
                    <div class="personal-favorit__status unavailable">Нет в наличии</div>
                </div>
                <div class="personal-favorit__cell">
                    <div class="personal-favorit__price">700 <span class="rubl">p</span></div>
                    <div class="personal-favorit__buy">
                        <button type="submit" class="btn-type btn-type--buy">Купить</button>
                    </div>
                    <div class="personal-favorit__reminds">
                        <span class="remind-text">Сообщить</span>
                        <div class="personal-favorit__price-change">
                            <div class="personal__hint">о снижении цены</div>
                        </div>
                        <div class="personal-favorit__stock">
                            <div class="personal__hint">о наличии</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <div class="personal-popup js-move-popup">
        <div class="popup-closer"></div>
        <div class="personal-popup__head">Выберите список</div>
        <div class="personal-popup__content">
            <form>
                <div class="custom-select">
                    <select class="custom-select__inn">
                        <option class="custom-select__i">На ДР</option>
                        <option class="custom-select__i">Для дачи</option>
                        <option class="custom-select__i">Ремонт</option>
                    </select>
                </div>
                <button type="submit" class="btn btn--default">Сохранить</button>
            </form>
        </div>
    </div>

    <div class="personal-popup js-create-popup">
        <div class="popup-closer"></div>
        <div class="personal-popup__head">Создать список</div>
        <div class="personal-popup__content">
            <form>
                <div class="form-group">
                    <input class="input-control" type="text" placeholder="Название списка">
                </div>
                <button type="submit" class="btn btn--default">Сохранить</button>
            </form>
        </div>
    </div>

    <div class="personal-popup js-del-popup">
        <div class="popup-closer"></div>
        <div class="personal-popup__head">Удалить список</div>
        <div class="personal-popup__list-name">На ДР</div>
        <div class="personal-popup__content">
            <form>
                <button type="submit" class="btn btn--default">Удалить</button>
            </form>
        </div>
    </div>
    <div class="personal-popup js-share-popup">
        <div class="popup-closer"></div>
        <div class="personal-popup__head">Поделиться списком</div>
        <div class="personal-popup__list-name">На ДР</div>
        <div class="personal-popup__content">
            <ul class="personal__sharings">
                <li class="personal-share"><i class="personal-share__icon twitter"></i></li>
                <li class="personal-share"><i class="personal-share__icon fb"></i></li>
                <li class="personal-share"><i class="personal-share__icon vk"></i></li>
                <li class="personal-share"><i class="personal-share__icon gplus"></i></li>
                <li class="personal-share"><i class="personal-share__icon ok"></i></li>
                <li class="personal-share"><i class="personal-share__icon mailru"></i></li>
                <li class="personal-share"><i class="personal-share__icon mail"></i></li>

            </ul>
        </div>
    </div>
    <div class="personal-popup js-share-goods-popup">
        <div class="popup-closer"></div>
        <div class="personal-popup__head">Поделиться 4 товарами</div>
        <div class="personal-popup__list-name">Самокат, Планшетный компьютер, Бумажный конструктор Jazwares Minecraft Papercraft «Дружелюбные мобы»</div>
        <div class="personal-popup__content">
            <ul class="personal__sharings">
                <li class="personal-share"><i class="personal-share__icon twitter"></i></li>
                <li class="personal-share"><i class="personal-share__icon fb"></i></li>
                <li class="personal-share"><i class="personal-share__icon vk"></i></li>
                <li class="personal-share"><i class="personal-share__icon gplus"></i></li>
                <li class="personal-share"><i class="personal-share__icon ok"></i></li>
                <li class="personal-share"><i class="personal-share__icon mailru"></i></li>
                <li class="personal-share"><i class="personal-share__icon mail"></i></li>

            </ul>
        </div>
    </div>


<!-- Ниже старая верстка -->
    <div class="table-favorites table table--border-cell-hor">
		<? if (!$products): ?>
			У вас нет избранных товаров
		<? endif ?>

		<? foreach ($products as $product): ?>
		<?
			$rowId = 'id-favoriteRow-' . $product->getUi() ?: uniqid();
		?>

		<div class="table-row <?= $rowId ?>">
			<div class="table-favorites__cell-left table-cell">
				<a href="<?= $product->getLink() ?>">
					<img src="<?= $product->getImageUrl(1) ?>" alt="<?= $helper->escape($product->getName()) ?>" class="table-favorites__img">
				</a>
			</div>

			<div class="table-favorites__cell-center table-cell">
				<a href="<?= $product->getLink() ?>" class="table-favorites__name"><?= $product->getName() ?></a>
				<? if ($product->getPrice()): ?>
					<div class="table-favorites__price"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></div>
				<? endif ?>
			</div>

			<div class="table-favorites__cell-right table-cell">
                <? if ($product->getIsBuyable()) : ?>
				<?= $helper->render('cart/__button-product', [
					'product'  => $product,
					'onClick'  => isset($addToCartJS) ? $addToCartJS : null,
                    'noUpdate'  => true,
					//'sender'   => $buySender + ['from' => preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) == null ? $request->server->get('HTTP_REFERER') : preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER'))],
					//'sender2'  => $buySender2,
					'location' => 'user-favorites',
				]);// Кнопка купить ?>
                <? else : ?>
                    Нет в наличии
                <? endif ?>

				<!--<div class="btnBuy"><a href="" class="btn-type btn-type--buy js-orderButton jsBuyButton">В корзину</a></div>-->
				<div class="table-favorites__delete">
					<a data-ajax="true" href="<?= $helper->url('favorite.delete', ['productUi' => $product->getUi()]) ?>" class="jsFavoriteDeleteLink undrl" data-target=".<?= $rowId ?>">Удалить</a>
				</div>
			</div>
		</div>
		<? endforeach ?>
	</div>
</div>
