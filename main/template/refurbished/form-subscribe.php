<?php
/**
 * @var $page     \View\Layout
 * @var $request  \Http\Request
 * @var $form     \View\Refurbished\SubscribeForm
 * @var $redirect string
 */
?>

<?php
if (empty($redirect)) $redirect = $request->getRequestUri();
if (!isset($form)) $form = new \View\Refurbished\SubscribeForm();
?>

<form id="subscribe-form" action="<?= $page->url('refurbished.subscribe') ?>" class="form" method="post">
    <input type="hidden" name="redirect_to" value="<?= $redirect ?>"/>
    <div class="font14 mb70">
        <? if ($error = $form->getError('global')) echo $page->render('_formError', array('error' => $error)) ?>
        	<p>Мы предлагаем уникальную возможность для оптовых партнеров! Enter начинает распродажу уцененных товаров.</p>
			<p>Всю интересующую Вас информацию можете получить по телефонам  8 (910) 453-23-13 и 8 (915) 110-79-03</p>
 			<p>Подписка! Получай первым информацию о новых товарах и лотах</p>
            <? if ($error = $form->getError('name')) echo $page->render('_formError', array('error' => $error)) ?>
            <label class="mInlineBlock mb10">
            	<span class="mInlineBlock width70">Ваше имя</span>&nbsp;
            	<input type="text" id="subcriber_name" value="<?= $form->getName() ?>" name="subscriber[name]"/>
            </label><br />

            <? if ($error = $form->getError('email')) echo $page->render('_formError', array('error' => $error)) ?>
            <label class="mInlineBlock mb10">
            	<span class="mInlineBlock width70">Ваш e-mail</span>&nbsp;
            	<input type="text" id="subscriber_email" value="<?= $form->getEmail() ?>" name="subscriber[email]"/>
            </label>

        	<p id="subscribeSaleSubmit"><input class="button yellowbutton" type="submit" value="Подписаться" /></p>
    </div>
</form>
