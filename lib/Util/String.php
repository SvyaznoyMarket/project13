<?php

namespace Util;

class String {
    public static function ucfirst_utf8($str) {
        if($str{0} >= "\xc3") {
            return (
                ($str{1} >= "\xa0")
                ? ($str{0} . chr(ord($str{1}) - 32))
                : ($str{0} . $str{1})) . substr($str, 2);
        } else {
            return ucfirst($str);
        }
    }
}