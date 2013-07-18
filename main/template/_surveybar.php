<?php
/**
 * @var $page \View\Layout
 * @var $survey \Model\Survey\Entity
 */
?>

<div data-init-time="<?= $survey->getInitTime()->getTimeStamp() ?>" data-server-time="<?= (new \DateTime())->getTimeStamp() ?>" data-show-delay="<?= $survey->getShowDelay() ?>" data-is-time-passed="<?= (int)($survey->getIsTimePassed())?>" class="surveyBox<?= $survey->getIsTimePassed() ? '' : ' hf'?>">
  <a class="surveyBox__toggle" href="#">Показать опрос</a>
  <div class="surveyBox__content">
    <img class="pt20 pb20" src="/css/header/img/headerLogo.gif">
    <ul class="pb20">
      <li class="pb10 mBold surveyBox__question"><?= $survey->getQuestion() ?></li>
      <? foreach ($survey->getAnswers() as $key => $answer) { ?>
        <li class="pb5 surveyBox__answer"><a class="surveyBox__answer" data href="#"><?= $answer ?></a></li>
      <? } ?>
    </ul>
  </div>
</div>
