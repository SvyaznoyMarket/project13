<?
/**
 * @var $banners \Model\Banner\BannerEntity[]
 */
$isArrowsVisible = count($banners) > 4
?>

<? if (!empty($banners)) : ?>
<div class="slidesbnnr jsMainBannerWrapper">
    <ul class="slidesbnnr_lst jsMainBannerHolder">
        <? foreach ($banners as $key => $banner) : ?>
        <li class="slidesbnnr_i jsMainBannerImage">
            <a href="<?= $banner->url ?>" class="jsMainBannerLink slidesbnnr_lk"><img src="<?= $banner->getImageBig() ?>" alt="" class="slidesbnnr_img" /></a>
        </li>
        <? endforeach; ?>
    </ul>

    <div class="slidesbnnr_thmbs_wrap">
        <? if ($isArrowsVisible) : ?><div class="slidesbnnr_thmbs_btn slidesbnnr_thmbs_btn-top jsMainBannersButton jsMainBannersUpButton"></div><? endif ?>

        <ul class="slidesbnnr_thmbs jsMainBannerThumbsWrapper">
            <? foreach ($banners as $key => $banner) : ?>
            <li class="slidesbnnr_thmbs_i jsMainBannerThumb">
                <img class="slidesbnnr_thmbs_img <?= $key == 0 ? 'slidesbnnr_thmbs_img-act' : '' ?>"
                     src="<?= $banner->getImageSmall() ?>"
                     alt=""
                     data-timeout="1500"
                />
            </li>
            <? endforeach ?>
        </ul>

        <? if ($isArrowsVisible) : ?><div class="slidesbnnr_thmbs_btn slidesbnnr_thmbs_btn-bottom jsMainBannersButton jsMainBannersDownButton"></div><? endif ?>
    </div>
</div>
<? endif ?>