<div class="float100">
    <div class="column685 pr">
        <div class="line pb20 mt32"></div>
        <?php foreach($categoryList as $i => $child):?>
        <?php if($i>0): ?>
            <div class="line pb20 mt32"></div>
            <?php endif ?>
        <div class="serviceblock mNewSB">
            <div class="photo pr">
                <?php if ($child->getMediaImage()): ?>
                <div class="bServiceCard__eLogo"></div>
                <img class='bF1ServiceImg' alt="<?php echo $child->getName()?>" src="<?php echo $child->getMediaImageUrl() ?>">
                <?php else: ?>
                <img alt="<?php echo $child->getName()?>" src="/images/f1infobig.png">
                <?php endif ?>
            </div>
            <div class="info">
                <h3><?php echo $child->getName() ?></h3>
                <div class="hf">
                    <?php foreach($child->getServiceList() as $service): ?>
                    <div class="font16 pb8">
                        <a href="<?php echo $this->url('service.show', array('service'=>$service->getToken())) ?>" >
                            <?php echo $service->getName() ?>
                        </a>
                        <?php if (!is_null($service->getPrice())): ?> &mdash;
                        <div class="font16 mInlineBlock">
                            <strong>
                                <?php if($service->getPrice()): ?>
                                <?php echo number_format($service->getPrice(), 0, ',', ' ') ?>&nbsp;<span class="rubl">p</span>
                                <?php else: ?>
                                бесплатно
                                <?php endif ?>
                            </strong>
                        </div>
                        <?php endif ?>
                    </div>
                    <div class="pb20">
                        <?php echo $service->getDescription() ?>
                        <?php echo $service->getWork() ?>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>

<div class="column215">
    <h2>Выбираем услуги</h2>
    <div class="line pb10"></div>

    <?php foreach($categoryTree->getChildren() as $child):?>
    <h2><?php echo $child->getName() ?></h2>
    <ul class="leftmenu pb10">
        <?php foreach($child->getChildren() as $subChild):?>
        <li>
            <?php if($subChild->getId() == $category->getId()): ?>
            <strong class="motton"><?php echo $subChild->getName() ?></strong>
            <?php else: ?>
            <a href="<?php echo $subChild->getLink() ?>"><?php echo $subChild->getName() ?></a>
            <?php endif ?>
        </li>
        <?php endforeach ?>
    </ul>
    <?php endforeach ?>
</div>

