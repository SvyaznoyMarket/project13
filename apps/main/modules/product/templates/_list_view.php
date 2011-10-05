<?php if (false): ?>
<ul class="inline">
<?php foreach ($list as $item): ?>
  <li<?php if ($item['current']) echo ' class="current"' ?>><a href="<?php echo $item['url'] ?>"><?php echo $item['title'] ?></a></li>
<?php endforeach ?>
</ul>
<?php endif ?>
            <!-- View -->
            <div class="view">
                <span>Вид:</span>
                <?php foreach ($list as $item): ?>
                  <a href="<?php echo $item['url'] ?>" class="<?php echo $item['class'].($item['current'] ? ' active' : '') ?>" title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></a>
                <?php endforeach ?>
            </div>
            <!-- View -->
