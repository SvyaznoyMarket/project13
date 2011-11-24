    <div class="font11 pb10" style="font-weight: bold; color: #000;"><?php echo $property->name ?>: <span class="orange" style="text-decoration: underline;"><?php echo isset($property->current) ? $property->values[$property->current]['parameter']->getValue() : '' ?></span></div>
    <ul class="previewlist">
    <?php foreach ($property->values as $value): ?>
        <li><b<?php echo ($value['is_selected']) ? ' class="current"' : '' ?>><a href="<?php echo $value['url'] ?>"></a></b><img src="<?php echo $value['photo'] ?>" alt="<?php echo $value['parameter']->getValue() ?>" width="48" height="48" /></li>
    <?php endforeach ?>
    </ul>
    <div class="clear"></div>
        