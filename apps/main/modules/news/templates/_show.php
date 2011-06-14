<?php echo $news->name ?><br />
Опубликовано: <?php echo date("d-m-Y H:i", strtotime($news->published_at)); ?>