<?php if (false): ?>
  <div class="filter regionselect" style="float: none;">
    <form action="/" method="post" id="region"></form>
      <span class="fl">Регион:</span>
      <div class="filterchoice regionchoice">
          <a href="<?php echo $active['url']?>" class="regionlink"><?php echo $active['name']?></a>
          <div class="regionlist">
              <a href="<?php echo $active['url']?>" class="regionlink"><?php echo $active['name']?></a>
              <ul>
                  <?php foreach ($list as $item): ?>
                  <li><a href="<?php echo $item['url'] ?>" style="font-weight: normal;"><?php echo $item['name'] ?></a></li>
                  <?php endforeach ?>
              </ul>
          </div>
      </div>
  </div>
<?php endif ?>

<a href="<?php echo $active['url']?>" data-url="<?php echo url_for('region_init') ?>"><?php echo $active['name']?></a>
