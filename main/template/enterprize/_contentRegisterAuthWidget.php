<?php
// сюда добавляем код инициализации виджета и блок модального окна с формами
?>
<div id="enterprize-identify" class="popup" style="position: absolute; top: 40px; margin-top: 0px; left: 50%; margin-left: -374px; z-index: 1002; display: block;">
    <i class="close" title="Закрыть">Закрыть</i>

    <div class="bPopupTitle">Вход в Enter</div>

        <?=$page->render('form-login',[
            'form'  => $formAuth
        ])?>
        
        <?=$page->render('enterprize/form-registration',[
            'form'          => $formEnterprizeRegistration,
            'submitName'    => ''
        ])?>
</div>