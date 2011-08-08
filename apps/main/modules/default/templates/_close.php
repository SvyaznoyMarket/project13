<script type="text/javascript">

  window.parent.$.fn.colorbox.close()
<?php if ('true' == $sf_request->getParameter('reload-parent')): ?>
  window.parent.location.reload()
<?php endif ?>

</script>