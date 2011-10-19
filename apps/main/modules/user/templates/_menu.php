	<div class="column215">
          <?php  include_component('user', 'shortuserinfo') ?>

        <ul class="leftmenu pb10">
            <?php foreach ($leftMenu as $item): ?>
               <li>
                    <?php if ($item['current']): ?>
                        <strong class="orange">
                    <?php else: ?>
                        <a href="<?php echo url_for($item['url']) ?>">
                    <?php endif ?>

                   <?php echo $item['name'] ?>

                    <?php if ($item['current']): ?>
                        </strong>
                    <?php else: ?>
                        </a>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
        <a href="<?php echo url_for('user')?>" class="font11 underline">Вернуться в личный кабинет</a>
    </div>
    <div class="clear"></div>
