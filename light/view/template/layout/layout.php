<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="yandex-verification" content="623bb356993d4993" />
    <meta name="viewport" content="width=900" />
    <meta name="title" content="<?php echo $this->getTitle(); ?>" />
    <title><?php echo $this->getTitle(); ?></title>
    <meta name="description" content="<?php echo $this->getDescription(); ?>" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php $this->showCss() ?>
    <?php $this->showJS() ?>

    <?php if ($show_link) { ?>
    <link rel="canonical" href="<?php echo $rel_href; ?>" />
    <?php } ?>


    <?php echo $this->renderFile('default/_googleAnalytics') ?>

</head>

<body data-template="<?php echo isset($_template)?$_template:'default' ?>">

<?php
    if(\light\Config::get('debug'))
    {
        echo $this->renderFile('debug');
    }
?>

<div class="allpage" id="page">
    <div class="adfoxWrapper" id="adfoxbground"></div>

    <div class="allpageinner">

        <?php echo $this->renderFile('default/_header', array('categoryRootList' => $categoryRootList)) ?>

        <!-- Page head -->
        <?php echo $this->renderFile('default/_page_head', array(
            'pageTitle' => isset($pageTitle)?$pageTitle:Null,
            'breadCrumbList' => isset($breadCrumbList)?$breadCrumbList:array()
        )) ?>
        <!-- Page head -->

        <?php echo $this->renderFile($page, $data); ?>

        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>


<?php echo isset($wpFooter)?$wpFooter:Null; ?>

<!-- Lightbox -->
<div class="lightbox">
    <div class="lightboxinner">
        <div class="dropbox" style="left:733px; display:none;">
            <p>Перетащите сюда</p>
        </div>
        <!-- Flybox -->
        <ul class="lightboxmenu">
            <li class="fl"><a href="<?php echo $this->url('user.signin') ?>" class="point point1"><b></b>Личный кабинет</a></li>
            <li><a href="<?php echo $this->url('cart.index') ?>" class="point point2"><b></b>Моя корзина<span class="total" style="display:none;"><span id="sum"></span> &nbsp;<span class="rubl">p</span></span></a></li>
        </ul>
    </div>
</div>
<!-- /Lightbox -->

<?php echo $this->renderFile('region/_select', array('regionTopList' => $regionTopList)) ?>
<script src="/js/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="/js/LAB.min.js" type="text/javascript"></script>
<script src="/js/loadjs.js" type="text/javascript"></script>
<?php echo $this->renderFile('default/_auth'/*, array('oAuthProviderList' => $oAuthProviderList)*/); ?>

<?php if (\light\Config::get('isProduction')): ?>
    <?php $this->renderFile('default/_yandexMetrika') ?>
<div id="adblender" class="jsanalytics"></div>
<?php endif ?>

<?php /*if (has_slot('seo_counters_advance')): ?>
    <?php include_slot('seo_counters_advance') ?>
    <?php endif */?>

<div id="gooReMaCategories" class="jsanalytics"></div>

<?php /*include_component('default', 'adriver')*/ ?>
</body>
</html>
