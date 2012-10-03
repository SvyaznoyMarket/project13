<style>
    .debug_panel {
        position: relative;
        right: 0px;
        top: 0px;
        background-color: #DDD;
        height: 25px;
        width: 100%;
        border: solid 1px black;
        z-index: 1001;
    }

    .debug_panel_tab {
        display: none;
        width: 100%;
        min-height: 300px;
        position: absolute;
        top: 26px;
        left: 0px;
        background-color: #EFEFEF;
        z-index: 1000;
        border: solid 1px black;
        padding: 20px;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tab_link').click(function() {
            var currentTab = $('div[tab_name="'+$(this).attr('tab')+'"]');
            var currentTabDisplay = currentTab.css('display');
            $('.debug_panel_tab').css('display', 'none');

            if(currentTabDisplay == 'none')
            {
                currentTab.css('display', 'block');
            }

            return false;
        });
    });
</script>

<div class="debug_panel">
    <a href="#" tab="environment" class="tab_link">
        <img src="/sf/sf_web_debug/images/config.png" />
        Окружение
    </a> |
    <a href="#" tab="request" class="tab_link">

        Запрос
    </a> |
    <a href="#" tab="response" class="tab_link">Ответ</a> |
    <a href="#" tab="setting" class="tab_link">Настройки</a> |
    <a href="#" tab="log" class="tab_link">
        <img src="/sf/sf_web_debug/images/log.png" />
        Логи
    </a> |

    <img src="/sf/sf_web_debug/images/memory.png" />
    <?php echo $memoryUsage; ?> |

    <a href="#" tab="time" class="tab_link">
        <img src="/sf/sf_web_debug/images/time.png" />
        <?php echo $totalTime; ?>
    </a> |

    <a href="#" tab="core_request" class="tab_link">
        <img src="/sf/sf_web_debug/images/database.png" />
        <?php echo $coreRequestCount ?>
    </a>

</div>

<div class="debug_panel_tab" tab_name="environment">
    <h2>
        Окружение
    </h2>
    <br />

    <strong>Система:</strong> <?php echo $systemName; ?><br />
    <strong>PHP:</strong> <?php echo $phpVersion; ?><br />
    <strong>PHP INI:</strong> <?php echo $phpIniFile; ?><br />
    <strong>PHP SAPI:</strong> <?php echo $phpSAPIName; ?><br />
    <strong>Модули:</strong><br />
    <?php foreach($extensionList as $extension) { ?>
        - <?php echo $extension; ?> <br />
    <?php } ?>
</div>

<div class="debug_panel_tab" tab_name="request">
    <h2>
        Запрос
    </h2>

    <strong>Заголовки</strong><br />
    <?php foreach($requestHeaderList as $index => $value) { ?>
        <?php echo $index; ?>: <?php echo $value; ?><br />
    <?php } ?>

    <br />
    <strong>Параметры</strong><br />
    <?php foreach($requestParameterList as $index => $value) { ?>
        <?php echo $index; ?>: <?php echo $value; ?><br />
    <?php } ?>
</div>

<div class="debug_panel_tab" tab_name="response">
    <h2>Ответ</h2>

    <strong>Заголовки</strong><br />
    <?php foreach($responseHeaderList as $index => $value) { ?>
        <?php echo $index; ?>: <?php $value; ?><br />
    <?php } ?>
</div>

<div class="debug_panel_tab" tab_name="setting">
    <h2>Настройки</h2>

    <strong>Параметры</strong><br />
    <?php foreach($settingParameterList as $index => $value) { ?>
        <?php echo $index; ?>: <?php echo $value; ?><br />
    <?php } ?>
</div>

<div class="debug_panel_tab" tab_name="log">
    <h2>Логи</h2>

    <?php foreach($messageList as $message) { ?>
        <?php echo $message; ?><br /><hr /><br />
    <?php } ?>
</div>

<div class="debug_panel_tab" tab_name="time">
    <h2>Время выполнения</h2>

    <table border="1">
        <tr>
            <td><strong>Вызов</strong></td>
            <td><strong>Время</strong></td>
        </tr>

        <?php foreach($timeList as $call => $time) { ?>
            <tr>
                <td>
                    <?php echo $call; ?>
                </td>
                <td>
                    <?php echo $time[0]['delta']; ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<div class="debug_panel_tab" tab_name="core_request">
    <h2>Запросы к API ядра</h2>

    <?php foreach($coreClientMessageList as $message) { ?>
        <?php echo $message; ?><br /><hr /><br />
    <?php } ?>
</div>