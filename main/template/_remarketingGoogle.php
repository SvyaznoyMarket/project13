<?php
/**
* @var $tag_params []
*/
$tag_params = array_merge(['prodid' => '', 'pagetype' => 'default', 'pname' => '', 'pcat' => '', 'value' => ''], $tag_params);
?>
<? //if (\App::config()->analytics): ?>
<!-- Google Code for 'Тег ремаркетинга' -->
<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
    var google_tag_params = {
<?php $count = count($tag_params); $i = 0; foreach ($tag_params as $param => $value): $i++; ?>
            <?= $param ?>: <?= is_array($value) ? ("['" . implode($value, "', '") . "']") : ("'" . $value . "'") ?><?= ($i < $count) ? ",\r\n" : "\r\n" ?>
<?php endforeach; ?>
    }
    /* <![CDATA[ */
    var google_conversion_id = 1001659580;
    var google_conversion_label = "nphXCKzK6wMQvLnQ3QM";
    var google_custom_params = window.google_tag_params;
    var google_remarketing_only = true;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1001659580/?value=0&amp;label=nphXCKzK6wMQvLnQ3QM&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>
<? //endif ?>
