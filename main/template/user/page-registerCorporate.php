<?php
/**
 * @var $page           \View\User\CorporateRegistrationPage
 * @var $form           \View\User\CorporateRegistrationForm
 * @var $rootCategories \Model\Product\Category\Entity[]
 */
?>

<?= $page->render('form-registerCorporate', ['form' => $form, 'rootCategories' => $rootCategories, 'content' => $content]) ?>

<br class="clear" />

<p>&nbsp;</p>