<?php if (isset($service['main_photo'])) { ?>
    <div class="pb5">
      <img src="<?php echo $service['main_photo'] ?>" />
    </div>  
<?php } ?>
<div class="pb5">
  <?php echo $service['description'] ?>
</div>    
<div class="pb5">
  <?php echo $service['work']; ?>
</div>

<?php if (isset($service['currentPrice']) && $service['currentPrice']) { ?>
    <div class="font16 pb10">
        <strong><?php echo $service['currentPrice']; ?> Р</strong>
    </div>
<?php } ?>

