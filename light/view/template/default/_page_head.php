<div class="pagehead">
  <div class="breadcrumbs">
      <?php
        if(isset($breadCrumbList))
        {
      ?>
            <a href="/">Enter.ru</a> >
      <?php
            do {
                $item = array_shift($breadCrumbList);
      ?>
            <?php if(!empty($breadCrumbList)) { ?>
                <a href="<?php echo $item['url']?>">
            <?php } else { ?>
                <strong>
            <?php } ?>
            <?php echo $item['name']?></a>
            <?php if(!empty($breadCrumbList)) { ?>
                </a>
            <?php } else { ?>
                </strong>
            <?php } ?>

            <?php echo !empty($breadCrumbList)?' > ':Null;?>
      <?php
            } while (!empty($breadCrumbList));
        }
      ?>
  </div>
  <div class="clear"></div>
  <?php if(isset($pageTitle)) { ?>
    <h1><?php echo $pageTitle ?></h1>
  <?php } ?>
  <noindex>  
      <div class="searchbox">
        <?php echo $this->renderFile('search/_form.php', array(
          'wide' => isset($wide)?$wide:Null,
          'searchString' => isset($searchString)?$searchString:Null,
          'view' => isset($view)?$view:Null
      )); ?>
      </div>
  </noindex>    
  <div class="clear"></div>
</div>