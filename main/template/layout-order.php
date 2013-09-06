<?php
/**
 * @var $page \View\DefaultLayout
 */
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noyaca"/>

    <script type="text/javascript">
        window.htmlStartTime = new Date().getTime();
        document.documentElement.className = document.documentElement.className.replace("no-js","js");
    </script>

    <?= $page->slotMeta() ?>
    <title><?= $page->getTitle() ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon.png">
    <?= $page->slotMobileModify() ?>
    <?= $page->slotStylesheet() ?>
    <?= $page->slotHeadJavascript() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>
</head>

<body data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>
<?= $page->slotConfig() ?>

<div class="graying" style="opacity: 0.5; display: none;"></div>

<!-- js templete -->
<script type="text/html" id="moveitem_tmpl">
  <span class="bButtonPopup" style="left: 203px">
    <span class="bButtonPopup__eTitle">Переместить товар:</span>
      <% for ( var i = 0; i < dlvr . length; i++ ) { %>
      <a class="bButtonPopup__eLine moveline"><%=dlvr[i].title%></a>
      <% } %>
  </span>
</script>

<script type="text/html" id="tip_tmpl">
  <span class="bTooltip" style="top:-38px; left:<%=cssl%>">
    <span class="bTooltip__eText"><%=tiptext%></span>
    <span class="bTooltip__eArrow"></span>
  </span>
</script>
<!-- js templete -->

<div class="allpage" data="privet">
    <div class="allpageinner buyingpage">
        <?= $page->slotContent() ?>
    </div>
</div>

<?= $page->slotRegionSelection() ?>
<? if (!(bool)\App::exception()->all()) echo $page->render('order/_footer') ?>
<?= $page->slotFooter() ?>
<?= $page->slotSurveybar() ?>

<?= $page->slotBodyJavascript() ?>
<?= $page->slotInnerJavascript() ?>
<?= $page->slotAuth() ?>
<?= $page->slotYandexMetrika() ?>
<?= $page->slotAdvanceSeoCounter() ?>
<?= $page->slotAdriver() ?>
<?= $page->slotPartnerCounter() ?>

</body>
</html>
