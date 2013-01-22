<?php
/**
 * @var $page         \View\Layout
 * @var $myThingsData array
 */
?>

<script type="text/javascript">
    function _mt_ready(){
        if (typeof(MyThings) != "undefined") {
            MyThings.Track($('#myThingsTracker').data('value'));
        }
    }
</script>

<div id="myThingsTracker" class="jsanalytics" data-value="<?= $page->json($myThingsData) ?>"></div>