<!-- Filter -->
<div id="sorting" class="filter" data-sort="<?php echo $active['name'].'-'.$active['direction'] ?>">
    <span class="fl">Сортировать:</span>
    <div class="filterchoice">
        <a href="<?php echo $active['url']?>" class="filterlink"><?php echo $active['title']?></a>
        <div class="filterlist">
            <a href="<?php echo $active['url']?>" class="filterlink"><?php echo $active['title']?></a>
            <ul>
                <?php foreach ($list as $item): ?>
                <li><a href="<?php echo $item['url'] ?>"><?php echo $item['title'] ?></a></li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>
<!-- /Filter -->