<?php

namespace Util;

class Date {
    public static function strftimeRu($format, $timestamp) {
        if (false !== strpos($format, '%B2')) {
            $months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
            $format = str_replace('%B2', $months[date('n', $timestamp) - 1], $format);
        }

        return strftime($format, $timestamp);
    }
}