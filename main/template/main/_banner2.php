<?
/**
 * @var $banners array
 */
?>
<div class="slidesbnnr">
    <ul class="slidesbnnr_lst">
        <? foreach ($banners as $key => $banner) : ?>
        <li class="slidesbnnr_i">
            <a href="<?= @$banner['url'] ?>" class="slidesbnnr_lk"><img src="<?= @$banner['imgb'] ?>" alt="" class="slidesbnnr_img" /></a>
        </li>
        <? endforeach; ?>
    </ul>

    <ul class="slidesbnnr_thmbs">
        <? foreach ($banners as $key => $banner) : ?>
        <li class="slidesbnnr_thmbs_i">
            <img class="slidesbnnr_thmbs_img <?= $key == 0 ? 'slidesbnnr_thmbs_img-act' : '' ?>" src="<?= @$banner['imgs'] ?>" alt="" />
        </li>
        <? endforeach; ?>
    </ul>
</div>