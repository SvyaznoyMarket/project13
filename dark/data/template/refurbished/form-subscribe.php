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

        <? if ($error = $form->getError('global')) echo $page->render('_formError', array('error' => $error)) ?>

            <? if ($error = $form->getError('name')) echo $page->render('_formError', array('error' => $error)) ?>
            <input type="text" id="subcriber_name" value="<?= $form->getName() ?>" name="subscriber[name]"/>

            <? if ($error = $form->getError('email')) echo $page->render('_formError', array('error' => $error)) ?>
            <input type="text" id="subscriber_email" value="<?= $form->getEmail() ?>" name="subscriber[email]"/>

        <input type="submit" value="Подписаться" tabindex="4"/>
</form>