<?php
namespace View\Livetex;


class HtmlChatHistoryContent extends HtmlBasicContent {

    protected $count_iterations = 0;
    protected $count_positive_votes = 0;
    protected $count_negative_votes = 0;
    protected $count_messages = 0;
    protected $count_noanswer = 0;

    protected $duration_cross_operators = [];

    protected $chat_times = 0;

    protected $count_first_answers = 0;
    protected $first_answers_time = 0;


    protected function inCycleCondition( &$item ) {
        return isset( $item->id );
    }


    protected function inCycle( $item ) {
        $out = '';
        $this->count_iterations++;

        $operId = $item->member;
        if ( $operId ) {
            $item->member = $this->operator_info($operId, 'name') . " (ID: $operId)";
            $item->member = $this->operator_link($operId, $item->member);
        }

        $vvote = 'Оценка не поставлена';
        if ($item->vvote == 1) {
            $vvote = 'Положительная оценка';
            $this->count_positive_votes++;
        } else if ($item->vvote == 2) {
            $vvote = 'Отрицательная оценка';
            $this->count_negative_votes++;
        }

        $mvote = 'Оценка не поставлена';
        if ($item->mvote == 1) {
            $mvote = 'Положительная оценка';
        } else if ($item->vvote == 2) {
            $mvote = 'Отрицательная оценка';
        }

        print '<pre>!# --- { ';
        print_r($item->firstanswer);
        print ' | ';
        print_r($item->chattime);
        print ' | ';
        print_r($item->count);
        print '} --</pre>';


        $out .= '<div class="id_oper"><span class="param_name">Идентификатор чата: </span>' . $item->id . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор посетителя: </span>' . $item->visitor . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Оператор: </span>' . $item->member . '</div>';

        $out .= '<div class="id_oper"><span class="param_name">Оценка чата посетителем: </span>' . $vvote . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор оценки чата оператором: </span>' . $mvote . '</div>';

        $out .= '<div class="id_oper"><span class="param_name">Дата: </span>' . date($this->date_format, $item->timestamp) . '</div>';

        if ( !empty($item->firstanswer) ) {
            $s = $this->timeInSeconds( $item->firstanswer );
            $this->first_answers_time = (int) $this->first_answers_time + (int) $s;
            $this->count_first_answers++;
            $item->firstanswer = (string) $s . ' c ';
        }
        $out .= '<div class="id_oper"><span class="param_name">Время ответа на первое сообщение посетителя: </span>' . $item->firstanswer . '</div>';

        //$item->chattime = $this->timeInSeconds( $item->chattime );

        if ( $operId ) {
            $item->chattime = $this->timeInSeconds($item->chattime);
            /*if (!isset( $this->duration_cross_operators[$operId] )) {
                $arr = [
                    'count_chats' => 1,
                    'all_chattime' => $item->chattime,
                ];
            }else{
                $arr = [
                    'count_chats' => 1 + $this->duration_cross_operators[$operId]['count_chats'],
                    'all_chattime' => $item->chattime + $this->duration_cross_operators[$operId]['all_chattime'],
                ];
                //$arr['count_chats'] += $this->duration_cross_operators[$operId]['count_chats']
            }
            $this->duration_cross_operators[$operId] = $arr;
            */

            $this->duration_cross_operators[$operId]['count_chats'] += 1; // кол-во чатов
            $this->duration_cross_operators[$operId]['all_chattime'] += $item->chattime; // общее время чатов

            if ( (!$item->count) || (!$item->chattime) ) {
                $this->duration_cross_operators[$operId]['unanswered'] += 1; // не отвеченные чаты
            }

        }

        $out .= '<div class="id_oper"><span class="param_name">Длительность чата: </span>' . $this->timeFromSeconds($item->chattime) . '</div>';
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


    protected function analytics() {
        $out = '';

        $average = round( $this->chat_times / $this->count_iterations , 2);
        $out .= '<p> Cреднее время чата: ' . $this->timeFromSeconds($average) . '. Всего чатов: '.$this->count_iterations.'</p>';

        $average = round( $this->count_messages / $this->count_iterations , 2);
        $out .= '<p> Cреднее количество сообщений в диалоге: '.$average.'.</p>';

        $average = round( $this->first_answers_time / $this->count_first_answers , 2);
        $out .= '<p> Cреднее время первого ответа: ' . $this->timeFromSeconds($average) . '. Всего ответов было: ' . $this->count_first_answers . '</p>';

        $out .= '<p>Количество положительных оценок: ' . $this->count_positive_votes.'.</p>';
        $out .= '<p>Количество отрицательных оценок: ' . $this->count_negative_votes.'.</p>';

        $out .= '<p>Количество чатов с одним сообщением (без ответа оператора): ' . $this->count_noanswer.'.</p>';


        $ii = 0;


        $opers = '<div class="durations">';
        $opers .= '<h3>Продолжительность разговора оператора</h3>';


        $opers .= '<table>';
        $opers .= '<th>Агент</th>';
        $opers .= '<th>Статус</th>';
        $opers .= '<th>Диалогов</th>';
        $opers .= '<th>Длительность</th>';
        $opers .= '<th>В среднем</th>';

        foreach($this->duration_cross_operators as $id => $val) {
            $opers .= '<tr>';

            $count = $val['count_chats'];
            $all = $val['all_chattime'];
            $unanswered = $val['unanswered'];
            $average = ( $count ) ? $all/$count : null;


            $opers .= '<td>';
            $opers .= $this->operator_link($id, $this->operator_info($id) );
            $opers .= '</td>';


            $opers .= '<td>';
            $opers .= $this->operator_link($id, $this->operator_info($id, 'isonline') ) ? 'Онлайн' : 'Офлайн' ;
            $opers .= '</td>';



            $opers .= '<td>';
            if ($count) {
                $opers .=  $count;
            }
            $opers .= '</td>';



            $opers .= '<td>';
            if ($all) {
                $opers .= $this->timeFromSeconds( $all );
            }
            $opers .= '</td>';



            $opers .= '<td>';
            if ($average) {
                $opers .= $this->timeFromSeconds( $average );
            }
            $opers .= '</td>';


            $opers .= '</tr>';
        }

        $opers .= '</table>';



        $opers .= '</div>';


        $out .= $opers;

        return $out;
    }

}