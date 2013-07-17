<?php
/**
 * @var $page \View\Layout
 * @var $survey \Model\Survey\Entity
 */
?>

<!-- Lightbox -->
<div class="surveyBox">
    <div class="surveyBox__toggle"><a href="#">Показать опрос</a></div>
    <div class="surveyBox__content">
        <img src="/css/header/img/headerLogo.gif">
        <ul>
            <li class="surveyBox__question">Test question<? //= $survey->getQuestion() ?></li>
            <? //foreach ($survey->getAnswers() as $key => $answer) { ?>
                <li class="surveyBox__answer">Test answer<? //= $answer ?></li>
            <? //} ?>
        </ul>
    </div>
</div>
