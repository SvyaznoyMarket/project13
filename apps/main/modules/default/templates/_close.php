<script type="text/javascript">

<?php if ('true' == $sf_request->getParameter('reload-parent')): ?>
  window.parent.location.reload()
<?php elseif (!empty($url)): ?>
  window.opener.location = '<?php echo $url ?>'
  window.close()
<?php endif ?>

  window.parent.$.fn.colorbox.close()

</script>