<?
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $brand                  \Model\Brand\Entity|null
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $hotlinks               array
 * @var $seoContent             string
 * @var $relatedCategories      array
 * @var $categoryConfigById     array
 * @var $slideData              array
 */
?>
<!-- для внутренних страниц добавляется класс middle_transform -->
<div class="middle middle_transform">
    <div class="container">
        <main class="content">
            <div class="content__inner">
                <!-- баннер -->
                <div class="banner-section">
                    <img src="http://content.adfox.ru/150713/adfox/176461/1346077.jpg" width="940" height="240" alt="" border="0">
                </div>
                <!--/ баннер -->

                <div class="section">
                    <ul class="bread-crumbs">
                        <li class="bread-crumbs__item"><a href="" class="bread-crumbs__link underline">Мебель</a></li>
                        <li class="bread-crumbs__item"><a href="" class="bread-crumbs__link underline">Мягкая мебель</a></li>
                        <li class="bread-crumbs__item">Кресла</li>
                    </ul>

                    <ul class="categories-grid grid-3col">

                        <? foreach ($category->getChild() as $childCategory) : ?>

                            <li class="categories-grid__item grid-3col__item">
                                <a href="" class="categories-grid__link">
                                    <span class="categories-grid__img">
                                        <img src="<?= $childCategory->getImageUrl() ?>" alt="" class="image">
                                    </span>

                                    <span class="categories-grid__text"><?= $childCategory->getName() ?></span>
                                </a>
                            </li>

                        <? endforeach ?>

                    </ul>
                </div>

                <hr class="hr-orange">

                <div class="fltrBtn fltrBtn-bt">
                    <form id="productCatalog-filter-form" class="js-category-filter" action="/catalog/electronics/telefoni-897" data-count-url="" method="GET">
                        <div class="fltrBtn_kit fltrBtn_kit--mark">
                            <div class="fltrBtn_tggl fltrBtn_kit_l">
                                <i class="fltrBtn_tggl_corner icon-corner"></i>
                                <span class="dotted">Бренд</span>
                            </div>

                            <div class="fltrBtn_kit_r ">
                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://8.imgenter.ru/uploads/media/ae/63/2d/393d44343d06f3ab2cd8564ca76d598c067e0a8f.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://a.imgenter.ru/uploads/media/b4/fc/a6/63bd2f7a1be1eae1c0e67343ccc063dc6572efb1.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://8.imgenter.ru/uploads/media/ae/63/2d/393d44343d06f3ab2cd8564ca76d598c067e0a8f.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://a.imgenter.ru/uploads/media/b4/fc/a6/63bd2f7a1be1eae1c0e67343ccc063dc6572efb1.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://8.imgenter.ru/uploads/media/ae/63/2d/393d44343d06f3ab2cd8564ca76d598c067e0a8f.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://a.imgenter.ru/uploads/media/b4/fc/a6/63bd2f7a1be1eae1c0e67343ccc063dc6572efb1.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <a href="#" class="fltrBtn_btn fltrBtn_btn-btn js-category-v2-filter-otherBrandsOpener"><span class="dotted">Ещё 26</span></a>

                                    <span class="js-category-v2-filter-otherBrands" style="display: inline;">
                                        <div class="fltrBtn_i ">
                                            <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-4298" name="f-brand-jinga" value="4298" data-name="Jinga">
                                            <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-4298">
                                                <span class="fltrBtn_btn_tx">Jinga</span>
                                                <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                            </label>
                                        </div>
                                    </span>
                            </div>
                        </div>

                        <div class="fltrBtn_kit fltrBtn_kit-box ">
                            <div class="fltrBtnBox fl-l js-category-v2-filter-dropBox js-category-v2-filter-dropBox-price">
                                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                    <span class="fltrBtnBox_tggl_tx dotted">Цена</span>
                                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                                </div>

                                <div class="fltrBtnBox_dd fltrBtnBox_dd-l">
                                    <ul class="fltrBtnBox_dd_inn lstdotted js-category-v2-filter-dropBox-content">
                                        <li class="lstdotted_i">
                                            <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-to=12800">
                                                <span class="txmark1">до</span> 12&thinsp;800
                                            </a>
                                        </li>

                                        <li class="lstdotted_i">
                                            <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-from=12801&amp;f-price-to=25200">
                                                <span class="txmark1">от</span> 12&thinsp;801
                                                <span class="txmark1">до</span> 25&thinsp;200
                                            </a>
                                        </li>

                                        <li class="lstdotted_i">
                                            <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-from=50001">
                                                <span class="txmark1">от</span> 50&thinsp;001
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="fltrBtn_kit fltrBtn_kit-box js-category-v2-filter-otherGroups">
                            <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                    <span class="fltrBtnBox_tggl_tx dotted">В магазине</span>
                                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                                </div>

                                <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                    <div class="fltrBtnBox_dd_inn">
                                        <div class="fltrBtn_param">
                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_radio js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-2" name="shop" value="2">
                                                <label class="custom-label" for="id-productCategory-filter-shop-option-2">
                                                    ул. Орджоникидзе, д. 11, стр. 10
                                                </label>
                                            </div>

                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_radio js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-13" name="shop" value="13">
                                                <label class="custom-label" for="id-productCategory-filter-shop-option-13">
                                                    Волгоградский пр-т, д. 119а.
                                                </label>
                                            </div>

                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_radio js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-68" name="shop" value="68">
                                                <label class="custom-label" for="id-productCategory-filter-shop-option-68">
                                                    Свободный пр-кт, д. 33
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                    <span class="fltrBtnBox_tggl_tx dotted">Платформа</span>
                                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                                </div>

                                <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                    <div class="fltrBtnBox_dd_inn">
                                        <div class="fltrBtn_param">
                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_check2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-5337" name="f-prop3826-android" value="5337">
                                                <label class="custom-label" for="id-productCategory-filter-prop3826-option-5337">
                                                    Android
                                                </label>
                                            </div>

                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_check2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-29929" name="f-prop3826-android_4_0_ics" value="29929">
                                                <label class="custom-label" for="id-productCategory-filter-prop3826-option-29929">
                                                    Android 4.0 ICS
                                                </label>
                                            </div>

                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_check2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-29928" name="f-prop3826-android_4_1_jelly_bean" value="29928">
                                                <label class="custom-label" for="id-productCategory-filter-prop3826-option-29928">
                                                    Android 4.1 Jelly Bean
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                    <span class="fltrBtnBox_tggl_tx dotted">Дисплей</span>
                                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                                </div>

                                <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                    <div class="fltrBtnBox_dd_inn">
                                        <div class="fltrBtn_param">
                                            <div class="fltrBtn_param_n">Диагональ экрана</div>

                                            <div class="fltrBtn_ln js-category-v2-filter-element-number">
                                                <span class="fltrBtn_param_lbl txmark1">от</span> <input class="fltrBtn_param_it js-category-v2-filter-element-number-from" name="" value="" placeholder="1.4" type="text">
                                                &ensp;<span class="fltrBtn_param_lbl txmark1">до</span> <input class="fltrBtn_param_it js-category-v2-filter-element-number-to" name="" value="" placeholder="6" type="text">
                                                <span class="fltrBtn_param_lbl txmark1">"</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="fltrBtn_kit fltrBtn_kit-nborder">
                            <div class="js-category-filter-selected clearfix">
                                <ul class="fltr_slctd">
                                    <li class="fltr_slctd_i fltr_slctd_i-n">В магазине:</li>

                                    <li class="fltr_slctd_i">
                                        <span>ул. Орджоникидзе, д. 11, стр. 10</span>
                                        <a class="btn-closer2 jsHistoryLink" href=""></a>
                                    </li>
                                </ul>

                                <ul class="fltr_slctd">
                                    <li class="fltr_slctd_i fltr_slctd_i-n">Память:</li>

                                    <li class="fltr_slctd_i">
                                        Встроенная память

                                        <span>от 5 ГБ</span>
                                        <a class="btn-closer2 jsHistoryLink" href=""></a>
                                    </li>
                                </ul>

                                <ul class="fltr_slctd">
                                    <li class="fltr_slctd_i fltr_slctd_i-n">Платформа:</li>

                                    <li class="fltr_slctd_i">
                                        <span>Android 4.1 Jelly Bean</span>
                                        <a class="btn-closer2 jsHistoryLink" href=""></a>
                                    </li>
                                </ul>

                                <a class="fltr_clsr jsHistoryLink" href="">
                                    <span class="btn-closer3"></span>
                                    <span class="dotted">Очистить все</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="sorting sorting-top js-category-sortingAndPagination">
                    <!-- Сортировка товаров по параметрам -->
                    <ul class="sorting_lst fl-l js-category-sorting">
                        <li class="sorting_i sorting_i-tl">Сортировать</li>

                        <li class="sorting_i act js-category-sorting-activeItem js-category-sorting-defaultItem js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="default-desc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928">Автоматически</a>
                        </li>
                        <li class="sorting_i js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="hits-desc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928&amp;sort=hits-desc">Хиты продаж</a>
                        </li>
                        <li class="sorting_i js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="price-asc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928&amp;sort=price-asc">По цене ▲</a>
                        </li>
                        <li class="sorting_i js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="price-desc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928&amp;sort=price-desc">По цене ▼</a>
                        </li>
                    </ul>

                    <ul class="sorting_lst fl-r js-category-pagination">
                        <li class="sorting_i sorting_i-tl">Страницы</li>

                        <li class="sorting_i act js-category-pagination-activePage js-category-pagination-page">
                            <a class="sorting_lk sorting_lk-page jsPagination" href="#">1</a>
                        </li>

                        <li class="sorting_i hidden js-category-pagination-paging"><a class="sorting_lk sorting_lk-page jsPaginationEnable" href="#">123</a></li>

                        <li class="sorting_i js-category-pagination-infinity"><a class="sorting_lk sorting_lk-page jsInfinityEnable" href="#">∞</a></li>
                    </ul>
                </div>

                <div class="section">
                    <div class="goods goods_grid goods_listing grid-3col">
                        <div class="goods__item grid-3col__item">
                            <div class="sticker-list">
                                <div class="sticker sticker_sale">Sale</div>
                            </div>

                            <div class="goods__controls">
                                <a class="add-control add-control_wish" href=""></a>
                                <a class="add-control add-control_compare" href=""></a>
                            </div>

                            <a href="" class="goods__img">
                                <img src="http://5.imgenter.ru/uploads/media/c9/2b/5f/thumb_85b6_product_160.jpeg" alt="" class="goods__img-image" style="opacity: 1;">
                            </a>

                            <div class="goods__rating rating">
                                    <span class="rating-state">
                                        <i class="rating-state__item rating-state__item_1 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_2 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_3 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_4 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_5 icon-rating"></i>
                                    </span>

                                <span class="rating-count">(10)</span>
                            </div>

                            <div class="goods__name">
                                <div class="goods__name-inn">
                                    <a href="">Мягкая игрушка Toivy «Лежащий полярный медведь», 28 см</a>
                                </div>
                            </div>

                            <div class="goods__price-old"><span class="line-through">330</span> ₽</div>

                            <div class="goods__price-now">157 ₽</div>

                            <a class="goods__btn btn-primary btn-primary_middle" href="">Купить</a>
                        </div>

                        <div class="goods__item grid-3col__item">
                            <div class="sticker-list">
                                <div class="sticker sticker_sale">Sale</div>
                                <div class="sticker sticker_sale" style="background: #000">Black Friday</div>
                            </div>

                            <div class="goods__controls">
                                <a class="add-control add-control_wish" href=""></a>
                                <a class="add-control add-control_compare" href=""></a>
                            </div>

                            <div class="sticker-brand">
                                <a href=""><img src="http://content.enter.ru/wp-content/uploads/2014/05/tchibo.png" alt=""></a>
                            </div>

                            <a href="" class="goods__img">
                                <img src="http://5.imgenter.ru/uploads/media/c9/2b/5f/thumb_85b6_product_160.jpeg" alt="" class="goods__img-image" style="opacity: 1;">

                                <div class="sticker sticker_info">Товар со склада</div>
                            </a>

                            <div class="goods__rating rating">
                                    <span class="rating-state">
                                        <i class="rating-state__item rating-state__item_1 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_2 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_3 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_4 icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_5 icon-rating"></i>
                                    </span>

                                <span class="rating-count">(10)</span>
                            </div>

                            <div class="goods__name">
                                <div class="goods__name-inn">
                                    <a href="">Мягкая игрушка Toivy «Лежащий полярный медведь», 28 см</a>
                                </div>
                            </div>

                            <div class="goods__price-old"><span class="line-through">330</span> ₽</div>

                            <div class="goods__price-now">157 ₽</div>

                            <a class="goods__btn btn-primary btn-primary_middle" href="">Купить</a>
                        </div>

                        <div class="goods__item grid-3col__item">
                            <div class="sticker-list">
                                <div class="sticker sticker_sale">Sale</div>
                                <div class="sticker sticker_sale" style="background: #000">Black Friday</div>
                            </div>

                            <div class="goods__controls">
                                <a class="add-control add-control_wish" href=""></a>
                                <a class="add-control add-control_compare" href=""></a>
                            </div>

                            <a href="" class="goods__img">
                                <img src="http://5.imgenter.ru/uploads/media/c9/2b/5f/thumb_85b6_product_160.jpeg" alt="" class="goods__img-image" style="opacity: 1;">
                            </a>

                            <div class="goods__rating rating">
                                    <span class="rating-state">
                                        <i class="rating-state__item rating-state__item_1 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_2 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_3 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_4 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_5 icon-rating"></i>
                                    </span>

                                <span class="rating-count">(10)</span>
                            </div>

                            <div class="goods__name">
                                <div class="goods__name-inn">
                                    <a href="">Мягкая игрушка Toivy «Лежащий полярный медведь», 28 см</a>
                                </div>
                            </div>

                            <div class="goods__price-old"><span class="line-through">330</span> ₽</div>

                            <div class="goods__price-now">157 ₽</div>

                            <a class="goods__btn btn-primary btn-primary_middle" href="">Купить</a>
                        </div>

                        <div class="goods__item grid-3col__item">
                            <div class="sticker-list">
                                <div class="sticker sticker_sale">Sale</div>
                                <div class="sticker sticker_sale" style="background: #000">Black Friday</div>
                            </div>

                            <div class="goods__controls">
                                <a class="add-control add-control_wish active" href=""></a>
                                <a class="add-control add-control_compare active" href=""></a>
                            </div>

                            <a href="" class="goods__img">
                                <img src="http://5.imgenter.ru/uploads/media/c9/2b/5f/thumb_85b6_product_160.jpeg" alt="" class="goods__img-image" style="opacity: 1;">
                            </a>

                            <div class="goods__rating rating">
                                    <span class="rating-state">
                                        <i class="rating-state__item rating-state__item_1 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_2 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_3 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_4 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_5 icon-rating"></i>
                                    </span>

                                <span class="rating-count">(10)</span>
                            </div>

                            <div class="goods__name">
                                <div class="goods__name-inn">
                                    <a href="">Комплект постельного белья с одеялом-покрывалом Бельвита "Ромашка"</a>
                                </div>
                            </div>

                            <div class="goods__price-old"><span class="line-through">330</span> ₽</div>

                            <div class="goods__price-now">157 ₽</div>

                            <a class="goods__btn btn-primary btn-primary_middle" href="">Купить</a>
                        </div>

                        <div class="goods__item grid-3col__item">
                            <div class="sticker-list">
                                <div class="sticker sticker_sale">Sale</div>
                            </div>

                            <div class="goods__controls">
                                <a class="add-control add-control_wish" href=""></a>
                                <a class="add-control add-control_compare" href=""></a>
                            </div>

                            <a href="" class="goods__img">
                                <img src="http://5.imgenter.ru/uploads/media/c9/2b/5f/thumb_85b6_product_160.jpeg" alt="" class="goods__img-image" style="opacity: 1;">
                            </a>

                            <div class="goods__rating rating">
                                    <span class="rating-state">
                                        <i class="rating-state__item rating-state__item_1 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_2 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_3 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_4 rating-state__item_fill icon-rating"></i>
                                        <i class="rating-state__item rating-state__item_5 icon-rating"></i>
                                    </span>

                                <span class="rating-count">(10)</span>
                            </div>

                            <div class="goods__name">
                                <div class="goods__name-inn">
                                    <a href="">Мягкая игрушка Toivy «Лежащий полярный медведь», 28 см</a>
                                </div>
                            </div>

                            <div class="goods__price-old"><span class="line-through">330</span> ₽</div>

                            <div class="goods__price-now">157 ₽</div>

                            <a class="goods__btn btn-primary btn-primary_middle" href="">Купить</a>
                        </div>
                    </div>
                </div>

                <div class="sorting sorting_bottom js-category-sortingAndPagination">
                    <ul class="sorting_lst fl-r js-category-pagination">
                        <li class="sorting_i sorting_i-tl">Страницы</li>

                        <li class="sorting_i act js-category-pagination-activePage js-category-pagination-page">
                            <a class="sorting_lk sorting_lk-page jsPagination" href="#">1</a>
                        </li>

                        <li class="sorting_i hidden js-category-pagination-paging"><a class="sorting_lk sorting_lk-page jsPaginationEnable" href="#">123</a></li>

                        <li class="sorting_i js-category-pagination-infinity"><a class="sorting_lk sorting_lk-page jsInfinityEnable" href="#">∞</a></li>
                    </ul>
                </div>

                <!-- вы смотерли - слайдер -->
                <div class="section section_bordered js-module-require" data-module="jquery.slick">
                    <div class="section__title">Вы смотрели</div>

                    <div class="section__content">
                        <div class="slider-section">
                            <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev-watched"></button>
                            <div class="goods goods_images goods_list grid-9col js-slider-goods js-slider-goods-watched" data-slick-slider="watched" data-slick='{"slidesToShow": 9, "slidesToScroll": 9}'>
                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>

                                <div class="goods__item grid-9col__item">
                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                    </a>
                                </div>
                            </div>
                            <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next-watched"></button>
                        </div>
                    </div>
                </div>
                <!--/ вы смотерли - слайдер -->

                <!-- SEO информация -->
                <div class="section section_bordered section_seo">
                    <p>Тут какой-то SEO-текст</p>
                </div>
                <!--/ SEO информация -->
            </div>
        </main>
    </div>

    <aside class="left-bar">
        <?= $page->blockNavigation() ?>
    </aside>
</div>
