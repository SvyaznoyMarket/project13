<?php
/**
 * @var $tests \Session\AbTest\Test[]|null
 */
?>
<style type="text/css">
    .test.highlighted {
        padding-top: 5px;
        padding-left: 10px;
        margin-top: -5px;
        margin-left: -10px;
        background: #dfe8e6;
    }
</style>

<div style="padding-left: 20px;">
    <h2>Переключение АБ-тестов</h2>
    <ul style="margin-bottom: 60px">
        <? foreach ($tests as $test) : ?>

        <li class="test" data-id="test-<?= $test->getKey() ?>"><span style="color: <?= $test->isActive() ? 'green' : 'red' ?>"><?= $test->name ?> (<?= $test->getKey() ?>)</span> <span style="color: gray;">(до <?= $test->getExpireDate() ?>)</span>
            <? if ($test->gaSlotNumber) : ?>
                <span style="color: #00ADE0">GA: slot <?= $test->gaSlotNumber ?>, scope <?= $test->gaSlotScope ?></span>
            <? endif; ?>
            <ul style="list-style-type: none; padding: 5px 0 10px 0;">
            <? foreach ($test->getCases() as $case) : ?>
                <li>
                    <label>
                        <input type="radio" class="changeCase" name="<?= $test->getKey() ?>" value="<?= $case->getKey() ?>" <?= $test->getChosenCase() == $case ? 'checked' : '' ?> <?= !$test->isActive() ? 'disabled' : '' ?> />
                        <?= $case->getName() ?> <span style="color: gray;">(<?= $case->getKey() ?>)</span> <span style="color: gray;"><?= $case->getTraffic() ?>%</span>
                    </label>
                    <span class="gaValue" style="color: #AAA; border: 1px solid #AAA; border-radius: 2px; font-size: 12px; padding: 0 2px; cursor: pointer" title="<?= $test->getKey().'_'.$case->getKey() ?>">GA</span>
                </li>
            <? endforeach ?>
            </ul>
        </li>

        <? endforeach ?>
    </ul>
</div>

<script type="text/javascript">

    $(document).ready(function(){
        if (location.hash) {
            $('.test[data-id="' + location.hash.slice(1) + '"]').addClass('highlighted');
        }

        var cookieDomain = '<?= \App::config()->session['cookie_domain'] ?>',
            cookieName = '<?= \App::config()->abTest['cookieName'] ?>';
//        $.cookie.json = true;

        $(document.body).on('click', '.changeCase', function(){

            var cookie = JSON.parse($.cookie(cookieName)),
                testName = $(this).attr('name'),
                testCase = $(this).val(),
                $test = $(this).closest('.test');

            if (testCase == '' || testName == '') return;

            cookie[testName] = testCase;

            $.cookie(cookieName, JSON.stringify(cookie), { expires: 365, path: '/', domain: cookieDomain });

            location.hash = $test.attr('data-id');
            location.reload();

        });

        $(document.body).on('click', '.gaValue', function(){
            var $this = $(this);
            $this.text($this.attr('title'));
        })
    })
</script>