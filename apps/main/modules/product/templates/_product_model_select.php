<?php if (false): ?>
    <div class="font11 pb10 mr10 fl"><?php echo $property->name ?></div>
    <div class="selectbox fl"><i></i>
    <select class="styled">
    <?php foreach ($property->values as $value): ?>
        <option<?php echo ($value['is_selected']) ? ' selected' : '' ?> data-ref="<?php echo $value['url'] ?>"><?php echo $value['parameter']->getValue() ?></option>
    <?php endforeach ?>
    </select>
    </div>
<?php endif ?>
<div class="filter">
    <span class="fl" style="font-weight: bold; color: #000;"><?php echo $property->name ?>:</span>
    <div class="filterchoice">
        <a href="<?php echo $property->values[$property->current]['url'] ?>" class="filterlink orange"><?php echo $property->values[$property->current]['parameter']->getValue() ?></a>
        <div class="filterlist">
            <a href="<?php echo $property->values[$property->current]['url'] ?>" class="filterlink orange"><?php echo $property->values[$property->current]['parameter']->getValue() ?></a>
            <ul>
                <?php foreach ($property->values as $key => $value): if ($property->current == $key): continue; endif; ?>
                <li><a href="<?php echo $value['url'] ?>"><?php echo $value['parameter']->getValue() ?></a></li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>