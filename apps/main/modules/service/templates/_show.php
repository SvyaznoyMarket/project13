<div class="pb5">
  <?php echo $service['description'] ?>
</div>    
<div class="pb5">
  <?php echo $service['work']; ?>
</div>

<?php if (isset($service['currentPrice']) && $service['currentPrice']>0) { ?>
    <div class="font16 pb10">
        <strong><?php echo $service['currentPrice']; ?> Р</strong>
    </div>
<?php } ?>

