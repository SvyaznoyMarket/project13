<?php
/**
 * @var $tests \Session\AbTest\Test[]|null
 */
?>
<h2>Переключение АБ-тестов</h2>
<ul>
    <? foreach ($tests as $test) : ?>

    <li><span style="color: <?= $test->isActive() ? 'green' : 'red' ?>"><?= $test->getKey() ?></span> <span style="color: gray;">(до <?= $test->getExpireDate() ?>)</span>
        <ul style="list-style-type: none; padding: 5px 0 10px 0;">
        <? foreach ($test->getCases() as $case) : ?>
            <li>
                <input type="radio" class="changeCase" name="<?= $test->getKey() ?>" value="<?= $case->getKey() ?>" <?= $test->getChosenCase() == $case ? 'checked' : '' ?> <?= !$test->isActive() ? 'disabled' : '' ?> />
                <?= $case->getName() ?> <span style="color: gray;"><?= $case->getTraffic() ?>%</span>
            </li>
        <? endforeach ?>
        </ul>
    </li>

    <? endforeach ?>
</ul>

<script type="text/javascript">

    $(document).ready(function(){

        var cookieDomain = '<?= \App::config()->session['cookie_domain'] ?>',
            cookieName = '<?= \App::config()->abTest['cookieName'] ?>';
//        $.cookie.json = true;

        $(document.body).on('click', '.changeCase', function(){

            var cookie = JSON.parse($.cookie(cookieName)),
                testName = $(this).attr('name'),
                testCase = $(this).val();

            if (testCase == '' || testName == '') return;

            cookie[testName] = testCase;

            $.cookie(cookieName, JSON.stringify(cookie), { expires: 365, path: '/', domain: cookieDomain });

            location.reload();

        })
    })
</script>