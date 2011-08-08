<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>

    <div class="container">
      <?php include_partial('default/logo') ?>
      <?php include_partial('default/region') ?>

      <br class="clear" />

      <div>
      <?php include_component('productCategory', 'list_root') ?>
      </div>

      <br class="clear" />

      <?php echo $sf_content ?>

    </div>

    <div style="display: none;">
      <div id="auth-form"><?php include_component('guardUser', 'form_signin') ?></div>
    </div>

    <?php include_partial('default/admin') ?>
  </body>
</html>
