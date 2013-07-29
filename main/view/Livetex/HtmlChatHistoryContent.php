<?php
namespace View\Livetex;


class HtmlChatHistoryContent extends HtmlBasicContent {

    protected $count_iterations = 0;
    protected $count_positive_votes = 0;
    protected $count_negative_votes = 0;
    protected $count_messages = 0;
    protected $count_noanswer = 0;

    protected $chat_times = 0;

    protected $count_first_answers = 0;
    protected $first_answers_time = 0;


    protected function inCycleCondition( &$item ) {
        return isset( $item->id );
    }


    protected function inCycle( $item ) {
        $out = '';

        $vvote = 'Оценка не поставлена';

        if ($item->vvote == 1) {
            $vvote = 'Положительная оценка';
            $this->count_positive_votes++;
        } else if ($item->vvote == 2) {
            $vvote = 'Отрицательная оценка';
            $this->count_negative_votes++;
        }

        $this->count_iterations++;


        $out .= '<div class="id_oper"><span class="param_name">Идентификатор чата: </span>' . $item->id . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор посетителя: </span>' . $item->visitor . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор оператора: </span>' . $item->member . '</div>';

        $out .= '<div class="id_oper"><span class="param_name">Оценка чата посетителем: </span>' . $vvote . '</div>';

        $out .= '<div class="id_oper"><span class="param_name">Идентификатор оценки чата оператором: </span>' . $item->mvote . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Дата: </span>' . date($this->date_format, $item->timestamp) . '</div>';

        if ( !empty($item->firstanswer) ) {
            $s = $this->timeInSeconds( $item->firstanswer );
            $this->first_answers_time = (int) $this->first_answers_time + (int) $s;
            $this->count_first_answers++;
            $item->firstanswer = (string) $s . ' c ';
        }

        $out .= '<div class="id_oper"><span class="param_name">Время ответа на первое сообщение посетителя: </span>' . $item->firstanswer . '</div>';

        //$item->chattime = $this->timeInSeconds( $item->chattime );

        $out .= '<div class="id_oper"><span class="param_name">Длительность чата в секундах: </span>' . $item->chattime . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор сайта, на котором происходил чат: </span>' . $item->site . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Имя посетителя: </span>' . $item->name . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Страна посетителя (определяется автоматически): </span>' . $item->country . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Город посетителя (определяется автоматически): </span>' . $item->city . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор группы оператора: </span>' . $item->group . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор первого сообщения: </span>' . $item->message . '</div>';

        $this->count_messages = $this->count_messages + $item->count;
        if ( $this->count_messages == 1 ) {
            $this->count_noanswer++;
        }

        $out .= '<div class="id_oper"><span class="param_name">Количество сообщений в чате: </span>' . $item->count . '</div>';

        $alink = base64_decode($item->referrer);
        $alink = '<a href="' . $alink . '">' . $alink . '</a>';
        $out .= '<div class="id_oper"><span class="param_name">Адрес страницы, с которой посетитель вызвал чат: </span>' . $alink . '</div>';

        $this->chat_times = $this->chat_times + $item->chattime;

        return $out;
    }



    protected function timeInSeconds($time) {
        if ( !is_numeric($time) ) {
            $answ = explode( ':', $time );

            $h = (int) $answ[0];
            $m = (int) $answ[1];
            $s = (int) $answ[2];

            if ($h) $m = $m + $h / 60;
            if ($m) $s = $s + $m / 60;

            return $s;

        }else{
            return $time;
        }
    }



    protected function analytics() {
        $out = '';

        $average = round( $this->chat_times / $this->count_iterations , 2);
        $out .= '<p> Cреднее время чата: '.$average.' секунд. Всего чатов: '.$this->count_iterations.'</p>';

        $average = round( $this->count_messages / $this->count_iterations , 2);
        $out .= '<p> Cреднее количество сообщений в диалоге: '.$average.'.</p>';

        $average = round( $this->first_answers_time / $this->count_first_answers , 2);
        $out .= '<p> Cреднее время первого ответа: '.$average.' секунд. Всего ответов было: ' . $this->count_first_answers . '</p>';

        $out .= '<p>Количество положительных оценок: ' . $this->count_positive_votes.'.</p>';
        $out .= '<p>Количество отрицательных оценок: ' . $this->count_negative_votes.'.</p>';

        $out .= '<p>Количество чатов с одним сообщением (без ответа оператора): ' . $this->count_noanswer.'.</p>';

        return $out;
    }

}