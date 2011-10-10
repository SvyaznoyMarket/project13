<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <?php include_title() ?>
  <?php include_stylesheets() ?>
  <?php include_javascripts() ?>
</head>

<body>
<div class="allpage">
<div class="allpageinner buyingpage">

    <!-- Header -->
    <div class="basketheader">
      <?php include_partial('default/logo') ?>
      <?php include_slot('step') ?>
        <div class="headerright">
            Заказ и консультации
            <div class="vcard"><div class="tel"><span>(495)</span>555-66-77</div></div>
        </div>
    </div>
    <!-- /Header -->


    <!-- Page head -->
    <div class="pagehead">
        <div class="breadcrumbs">
        <?php if (has_slot('navigation')): ?>
          <?php include_slot('navigation') ?>
        <?php endif ?>
        </div>
        <div class="clear"></div>
        <?php if (has_slot('title')): ?>
        <h1><?php include_slot('title') ?></h1>
        <?php endif ?>
        <div class="line"></div>
    </div>
    <!-- Page head -->

    <div class="clear"></div>

    <?php if (has_slot('receipt')): ?>
      <?php include_slot('receipt') ?>
    <?php endif ?>

    <?php echo $sf_content ?>

    <div class="clear"></div>

</div>
</div>

<!-- Footer -->
<div class="footer lowfooter">
    <div class="footerbottom">
        <div class="copy">
            &copy; &laquo;Enter&raquo; 2002-2011. Все права защищены.<br />
            Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.
            <div>
                <a href="">Политика конфидециальности</a>
                <a href="">Условия продажи в интернет-магазине</a>
                <b><i class="mistakeimg"></i><a href="" class="orange">сообщить!</a></b>
            </div>
        </div>
        <div class="counter">
            <a href=""><img src="/images/images/counter1.gif" alt="" width="80" height="30" /></a>
            <a href=""><img src="/images/images/counter2.gif" alt="" width="50" height="30" /></a>
            <a href=""><img src="/images/images/counter3.gif" alt="" width="50" height="30" /></a>
        </div>
    </div>
</div>
<!-- /Footer -->

</body>
</html>
