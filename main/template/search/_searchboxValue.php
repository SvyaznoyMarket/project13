<?php
/**
 * @var $searchQuery      string
 **/
?>

<? if(!empty($searchQuery)) { ?>
  <script type="text/javascript">
    $(document).ready(function(){
      if($('.searchtext').length) {
        $('.searchtext').val('<?= $searchQuery ?>');
        $('.searchtextClear').removeClass('vh');
      }
    });
  </script>
<? } ?>