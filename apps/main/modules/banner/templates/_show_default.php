<?php foreach ($list as $i => $item): ?>
  <?php if (0 == $i): ?>
    <div class="bigbanner">
      <a href=""><?php echo image_tag('banners/'.$item['image']) ?></a>
    </div>
  <?php else: ?>
    <div class="banner banner<?php echo ($i + 1) ?>">
      <a href="<?php echo $item['url'] ?>"><?php echo image_tag('banners/preview/'.$item['image_preview']) ?></a>
    </div>
  <?php endif ?>
<?php endforeach ?>
<!--<div class="prompt" style="top:450px; left:550px"><i></i>Серебряные серьги с аметистами<br />и фианитами<div class="font18">2 190 <span style="  font-family: 'PTSansRegular', serif; ">&#8399;</span></div></div>-->