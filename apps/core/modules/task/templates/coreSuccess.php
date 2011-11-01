<?php use_helper('I18N', 'Date') ?>
<?php include_partial('task/assets') ?>

<div id="sf_admin_container">
  <h1><pre><?php echo $query ?></pre></h1>

  <?php include_partial('task/flashes') ?>

  <div id="sf_admin_header"></div>

  <div id="sf_admin_content">
    <pre><?php echo $response ?></pre>
  </div>

  <div id="sf_admin_footer"></div>
</div>
