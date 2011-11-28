    <div class="f1linkbox" style="display:block;" >
        <a href="" class="f1link">Сервис F1</a> Сервис F1

        <!-- F1 -->
        <div class="hideblock width358" id="f1pp" style="display:none; top:0; left:0">
            <i title="Закрыть" class="close">Закрыть</i> 
            <div class="title">Добавление услуг F1</div>
            <form action="" class="form">
                <dl class="f1list">
                  <?php foreach ($f1 as $service):
                      if (!isset($service->id)) $service = ServiceTable::getInstance()->getById($service['id']);
                      ?>
                    <dt>
                        <label for="checkbox-<?php echo $service->id ?>"><?php echo $service->name ?></label>
                        <input ref="<?php echo $service->token ?>" id="checkbox-<?php echo $service->id ?>" name="service[<?php echo $service->id ?>]" type="checkbox" value="checkbox-<?php echo $service->id ?>" />
                        <b></b>
                    </dt>
                    <dd>
                        <?php echo $service->description ?><br />
                        Стоимость: <strong><?php echo number_format($service->getPriceByRegion(), 0, ',', ' ') ?> Р</strong>
                        <a href="<?php echo url_for('service_show',array('service' => $service->token)) ?>" class="underline">Подробнее</a>
                    </dd>                    
                 <?php  endforeach ?>                  
                </dl>
                <div class="fr pt5"><a href="<?php echo url_for('service_list') ?>" class="underline">Подробнее о Сервисе F1</a></div>
                <input type="button" class="button yellowbutton mr10" value="Выбрать" />
                <input type="reset" class="button whitebutton" value="Сбросить все" />
            </form>   
        </div>
        <!-- /F1 -->

    </div>