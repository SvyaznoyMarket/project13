<?php if (is_object($property->values[$property->current]['parameter'])): ?>
    <div class="bDropWrap">
      <h5><?php echo $property->name ?>:</h5>
      <div class="bDropMenu">
        <span class="bold"><a href="<?php echo $property->values[$property->current]['url'] ?>"><?php echo $property->values[$property->current]['parameter']->getValue() ?></a></span>

        <div>
          <span class="bold"><a href="<?php echo $property->values[$property->current]['url'] ?>"><?php echo $property->values[$property->current]['parameter']->getValue() ?></a></span>
          <?php foreach ($property->values as $key => $value): if ($property->current == $key) continue; ?>
            <span><a href="<?php echo $value['url'] ?>"><?php echo $value['parameter']->getValue() ?></a></span>
          <?php endforeach ?>
        </div>

      </div>
    </div>
<?php endif ?>