<?php
namespace View\Livetex;


class HtmlChatHistoryContent extends HtmlBasicContent {

    protected $count_iterations = 0;
    protected $count_chats = 0;
    protected $count_positive_votes = 0;
    protected $count_negative_votes = 0;
    protected $count_messages = 0;
    //protected $count_noanswer = 0;

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

        // Суммирование и сохранение общих данных по всем чатам
        $operId = $item->member;
        if ( $operId ) {
            $this->count_chats++; // Чатов с операторами
            $this->chat_times += $this->timeInSeconds($item->chattime); // Общее время чата с операторами
            $this->count_messages += $item->count; // Общее количество сообщений
            $item->member = $this->operator_info($operId, 'name') . " (ID: $operId)";
            $item->member = $this->operator_link($operId, $item->member);
        }

        $vvote_positive = $vvote_negative = 0;

        $vvote = 'Оценка не поставлена'; // mvote - оценка чата посетителем
        if ($item->vvote == 1) {
            $vvote = 'Положительная оценка';
            $this->count_positive_votes++;
            $vvote_positive = 1;
        } else if ($item->vvote == 2) {
            $vvote = 'Отрицательная оценка';
            $this->count_negative_votes++;
            $vvote_negative = 1;
        }

        //$mvote_positive = $mvote_negative = 0;

        $mvote = 'Оценка не поставлена'; // mvote - оценка чата оператором
        if ($item->mvote == 1) {
            $mvote = 'Положительная оценка';
            //$mvote_positive = 1;
        } else if ($item->vvote == 2) {
            $mvote = 'Отрицательная оценка';
            //$mvote_negative = 1;
        }


        $out .= '<div class="id_oper"><span class="param_name">Идентификатор чата: </span>' . $item->id . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор посетителя: </span>' . $item->visitor . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Оператор: </span>' . $item->member . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Оценка чата посетителем: </span>' . $vvote . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор оценки чата оператором: </span>' . $mvote . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Дата: </span>' . date($this->date_format, $item->timestamp) . '</div>';

        if ( !empty($item->firstanswer) ) {
            $s = $this->timeInSeconds( $item->firstanswer );
            if ( $operId ) {
                $this->first_answers_time = (int) $this->first_answers_time + (int) $s; // общее время ответов на первые сообщения
                $this->count_first_answers++; // кол-во реакций на первое сообщение
            }
            $item->firstanswer = (string) $s . ' c ';
        }
        $out .= '<div class="id_oper"><span class="param_name">Время ответа на первое сообщение посетителя: </span>' . $item->firstanswer . '</div>';

        $out .= '<div class="id_oper"><span class="param_name">Длительность чата: </span>' . $this->timeFromSeconds($item->chattime) . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор сайта, на котором происходил чат: </span>' . $item->site . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Имя посетителя: </span>' . $item->name . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Страна посетителя (определяется автоматически): </span>' . $item->country . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Город посетителя (определяется автоматически): </span>' . $item->city . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор группы оператора: </span>' . $item->group . '</div>';
        $out .= '<div class="id_oper"><span class="param_name">Идентификатор первого сообщения: </span>' . $item->message . '</div>';


        /*if ( $this->count_messages == 1 ) {
            $this->count_noanswer++;
        }*/

        $out .= '<div class="id_oper"><span class="param_name">Количество сообщений в чате: </span>' . $item->count . '</div>';

        $alink = base64_decode($item->referrer);
        $alink = '<a href="' . $alink . '">' . $alink . '</a>';
        $out .= '<div class="id_oper"><span class="param_name">Адрес страницы, с которой посетитель вызвал чат: </span>' . $alink . '</div>';



        /// Суммирование полученны данных по операторам:
        if ( $operId ) {
            $item->chattime = $this->timeInSeconds($item->chattime);

            if ( !isset( $this->duration_cross_operators[$operId] ) ) {
                // ели в массиве ранее не было оператора с таким айди, то создадим его прототип:
                $this->duration_cross_operators[$operId] = array(
                    'count_chats' => 0,
                    'all_chattime' => 0,
                    'unanswered' => 0,
                    'all_firstanswer' => 0,
                    'all_positive_votes' => 0,
                    'all_negative_votes' => 0,
                );
            }

            $this->duration_cross_operators[$operId]['count_chats'] += 1; // кол-во чатов
            $this->duration_cross_operators[$operId]['all_chattime'] += $item->chattime; // общее время чатов
            $this->duration_cross_operators[$operId]['all_firstanswer'] += $item->firstanswer; // общее время firstanswer
            $this->duration_cross_operators[$operId]['all_positive_votes'] += $vvote_positive; // кол-во положительных оценок чата посетителем
            $this->duration_cross_operators[$operId]['all_negative_votes'] += $vvote_negative; // кол-во отрицательных оценок чата посетителем

            if ( (!$item->count) || (!$item->chattime) ) { // условие, по которому можем считать чат неотвеченным
                $this->duration_cross_operators[$operId]['unanswered'] += 1; // не отвеченные чаты
            }

        }

        return $out;
    }


    protected function analytics() {
        $out = '';

        $out .= '</p>Всего чатов*: '.$this->count_iterations.". Чатов с операторами: $this->count_chats .</p>";

        $average = round( $this->chat_times / $this->count_chats , 2);
        $out .= '<p> Cреднее время чата с оператором: ' . $this->timeFromSeconds($average) . '; всё* время: ' . $this->timeFromSeconds($this->chat_times) . '.</p>';

        $average = round( $this->count_messages / $this->count_iterations , 2);
        $out .= '<p> Cреднее количество сообщений в диалоге (все чаты*): '.$average . '; всего* сообщений ' . $this->count_messages . '.</p>';

        $average = round( $this->first_answers_time / $this->count_first_answers , 2);
        $out .= '<p> Cреднее время ответа на первое сообщение: ' . $this->timeFromSeconds($average) . '. Количество реакций на первоё сообщение: ' . $this->count_first_answers . '</p>';

        $out .= '<p>Количество положительных оценок (все чаты*): ' . $this->count_positive_votes.'.</p>';
        $out .= '<p>Количество отрицательных оценок (все чаты*): ' . $this->count_negative_votes.'.</p>';
        //$out .= '<p>Количество чатов с одним сообщением (без ответа оператора): ' . $this->count_noanswer.'.</p>';

        $out .= '<p></p><p><em>* Существуют чаты без операторов (см первую строчку). Большинство данных статистики рассчитывается для чатов с операторами. '.
            'Отмеченные звёздочкой (*) параметры рассчитаны для всех чатов, в том числе для чатов, в которых не принял участие ни один оператор.</em></p><hr/>';

        $opers = '<div class="durations">';
        $opers .= '<h3>Продолжительность разговора оператора</h3>';

        $opers .= '<table>';
        $opers .= '<th>Агент</th>';
        $opers .= '<th>Статус</th>';
        $opers .= '<th>Отвеченных диалогов</th>';
        $opers .= '<th>Пропущенных диалогов</th>';
        $opers .= '<th>SLA</th>';
        $opers .= '<th>Среднее время ответа на первое сообщение</th>';
        $opers .= '<th>Кол-во положительных оценок посетителей</th>';
        $opers .= '<th>Кол-во отрицательных оценок посетителей</th>';
        //$opers .= '<th>Диалогов</th>';
        $opers .= '<th>Суммарная длительность диалогов</th>';
        $opers .= '<th>Средняя длительность диалога</th>';

        foreach($this->duration_cross_operators as $id => $val) {
            $opers .= '<tr>';

            $count_chats = $val['count_chats'];
            $all_chattime = $val['all_chattime'];
            $all_firstanswer = $val['all_firstanswer'];
            $unanswered = $val['unanswered'];
            $all_positive_votes = $val['all_positive_votes'];
            $all_negative_votes = $val['all_negative_votes'];
            $answered = $count_chats - $unanswered;

            if ($count_chats) {
                $SLA = (string) ( 100*round( $unanswered/ $count_chats , 3) ) . '%';
                $average = $this->timeFromSeconds( $all_chattime / $count_chats );
                $average_firstanswer = $this->timeFromSeconds( $all_firstanswer / $count_chats );
            } else {
                $SLA = 'none';
                $average = 'none';
            }


            $opers .= '<td>';
            $opers .= $this->operator_link($id, $this->operator_info($id) );
            $opers .= '</td>';


            $opers .= '<td>';
            $opers .= $this->operator_link($id, $this->operator_info($id, 'isonline') ) ? 'Онлайн' : 'Офлайн' ;
            $opers .= '</td>';

            $opers .= '<td>';
            $opers .= $answered;
            $opers .= '</td>';

            $opers .= '<td>';
            $opers .= $unanswered;
            $opers .= '</td>';

            $opers .= '<td>';
            $opers .= $SLA;
            $opers .= '</td>';

            $opers .= '<td>';
            $opers .= $average_firstanswer;
            $opers .= '</td>';

            $opers .= '<td>';
            $opers .= $all_positive_votes;
            $opers .= '</td>';

            $opers .= '<td>';
            $opers .= $all_negative_votes;
            $opers .= '</td>';

            /* // кол-во диалогов (чатов)
            $opers .= '<td>';
            if ($count_chats) {
                $opers .=  $count_chats;
            }
            $opers .= '</td>'; */


            $opers .= '<td>';
            if ($all_chattime) {
                $opers .= $this->timeFromSeconds( $all_chattime );
            }
            $opers .= '</td>';


            $opers .= '<td>';
            if ($average) {
                $opers .= $average;
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