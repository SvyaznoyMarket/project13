<?
/**
 * @var $banners array
 */
?>

<? if (!empty($banners)) : ?>
<div class="slidesbnnr">
    <ul class="slidesbnnr_lst jsMainBannerHolder">
        <? foreach ($banners as $key => $banner) : ?>
        <li class="slidesbnnr_i jsMainBannerImage">
            <a href="<?= @$banner['url'] ?>" class="slidesbnnr_lk"><img src="<?= @$banner['imgb'] ?>" alt="" class="slidesbnnr_img" /></a>
        </li>
        <? endforeach; ?>
    </ul>

    <ul class="slidesbnnr_thmbs">
        <? foreach ($banners as $key => $banner) : ?>
        <li class="slidesbnnr_thmbs_i jsMainBannerThumb">
            <img class="slidesbnnr_thmbs_img <?= $key == 0 ? 'slidesbnnr_thmbs_img-act' : '' ?>"
                 src="<?= @$banner['imgs'] ?>"
                 alt=""
                 data-timeout="<?= (int)@$banner['t'] ?>"
                />
        </li>
        <? endforeach; ?>
    </ul>
</div>
<? endif; ?>