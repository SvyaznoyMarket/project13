<?php

namespace Templating;

use Model\Product\Filter\Entity as FilterEntity;

class Helper {
    /**
     * @param array $replaces
     * @param array $excluded
     * @param null $route
     * @return mixed
     * @throws \RuntimeException
     */
    public function replacedUrl(array $replaces, array $excluded = null, $route = null) {
        $request = \App::request();

        if (null == $route) {
            if (!$request->attributes->has('route')) {
                throw new \RuntimeException('В атрибутах запроса не задан параметр route');
            }

            $route = $request->attributes->get('route');
        }

        $excluded = (null == $excluded) ? ['page' => '1'] : $excluded;

        $params = [];
        foreach (array_diff(array_keys($request->attributes->all()), ['pattern', 'method', 'action', 'route', 'require']) as $k) {
            $params[$k] = $request->attributes->get($k);
        }
        foreach ($request->query->all() as $k => $v) {
            $params[$k] = $v;
        }
        foreach ($replaces as $k => $v) {
            if (null === $v) {
                if (isset($params[$k])) unset($params[$k]);
                continue;
            }

            $params[$k] = $v;
        }

        $params = array_diff_assoc($params, $excluded);

        return \App::router()->generate($route, $params);
    }

    /**
     * @param int   $number  Например: 1, 43, 112
     * @param array $choices Например: ['отзыв', 'отзыва', 'отзывов']
     * @return mixed
     */
    public function numberChoice($number, array $choices) {
        $cases = [2, 0, 1, 1, 1, 2];

        return $choices[ ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * @param $price
     * @return string
     */
    public function formatPrice($price, $numDecimals = 0, $decimalsDelimiter = ',', $thousandsDelimiter = ' ') {
        return number_format($price, $numDecimals, $decimalsDelimiter, $thousandsDelimiter);
    }

    /**
     * @param $category
     * @param $productFilter
     * @return string
     */
    public function getFilterItemAllLink($category, $productFilter, $filter, $scrollTo) {
        $allLink = $category->getLink();
        $allLink .= preg_match('/.*\?.*/', $allLink) ? '&' : '?';
        $allLink .= 'scrollTo='.$scrollTo;
        foreach ($productFilter->dump() as $filterItem) {
            if(!in_array($filterItem[0], [$filter->getId(), 'is_view_list', 'category', 'is_model'])) {
                $allLink .= preg_match('/.*\?.*/', $allLink) ? '&' : '?';
                $allLink .= urlencode('f['.$filterItem[0].'][]').'='.(is_array($filterItem[2]) ? reset($filterItem[2]) : $filterItem[2]);
            }
        }

        return $allLink;
    }

    /**
     * @param $allLink
     * @param $option
     * @param $filter
     * @return string
     */
    public function getFilterItemOptionLink($allLink, $option, $filter) {
        $id = $option->getId();
        $optionLink = preg_match('/.*\?.*/', $allLink) ? $allLink.'&' : $allLink.'?';

        switch ($filter->getTypeId()) {
            case FilterEntity::TYPE_NUMBER:
            case FilterEntity::TYPE_SLIDER:
                if (!isset($values['to'])) {
                    $values['to'] = null;
                }
                if (!isset($values['from'])) {
                    $values['from'] = null;
                }
                if ($filter->getMax() != $values['to'] || $filter->getMin() != $values['from']) {
                    $optionLink .= urlencode('f['.strtolower($filter->getId()).'][from]').'='.$values['from'];
                    $optionLink .= urlencode('f['.strtolower($filter->getId()).'][to]').'='.$values['to'];
                }
                break;
            default:
                $optionLink .= urlencode('f['.strtolower($filter->getId()).'][]').'='.$id;
                break;
        }

        return $optionLink;
    }


    /**
     * @param $catalogJson
     * @param $category
     * @return string
     */
    function getCategoryLogoOrName($catalogJson, $category) {
        return !empty($catalogJson['logo_path']) && !empty($catalogJson['use_logo']) ? '<img src="'.$catalogJson['logo_path'].'">' : $category->getName();
    }

    /**
     * @param $string
     * @return string
     */
    public function nofollowExternalLinks($stringOriginal) {
        $stringWrapped = '<div>' . $stringOriginal . '</div>';
        $stringWrapped = str_replace('&', '&amp;', $stringWrapped);
        $stringWrapped = preg_replace('/<([a-z]*[^>\/a-z]+[^>]*)>/i', '&lt;$1&gt;', $stringWrapped);

        $dom = new \DOMDocument;
        $this->loadXML($stringWrapped, $dom, $stringOriginal);

        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            if (!preg_match('/enter\.ru/', $link->getAttribute('href')) && $link->getAttribute('rel') != 'nofollow') {
                $link->setAttribute('rel', 'nofollow');
            }
        }

        return $dom->saveHTML();
    }

    /**
     * @param string $stringWrapped
     * @param \DOMDocument $dom
     * @param string $stringOriginal
     */
    private function loadXML(&$stringWrapped, &$dom, $stringOriginal) {
        try {
            $dom->loadXML($stringWrapped);
        } catch (\Exception $e) {
            if(preg_match('/^.*mismatch: ([^ ]+) line.*$/i', $e->getMessage(), $matches) ||
                preg_match('/^.*attribute ([^ ]+) in.*$/i', $e->getMessage(), $matches)) {
                $brokenTag = array_pop($matches);
                $stringWrapped = preg_replace('/<([^<]*'.$brokenTag.'[^>]*)>/i', str_replace('>', '&gt;', str_replace('<', '&lt;', '<$1>')), $stringWrapped);
            } else {
                $stringWrapped = '<div>' . str_replace('>', '&gt;', str_replace('<', '&lt;', $stringOriginal)) . '</div>';
            }
            $this->loadXML($stringWrapped, $dom, $stringOriginal);
        }
    }

    /**
     * @param string $date
     * @return string
     */
    public function dateToRu($date) {
        $monthsEnRu = [
            'January' => 'января',
            'February' => 'февраля',
            'March' => 'марта',
            'April' => 'апреля',
            'May' => 'мая',
            'June' => 'июня',
            'July' => 'июля',
            'August' => 'августа',
            'September' => 'сентября',
            'October' => 'октября',
            'November' => 'ноября',
            'December' => 'декабря',
        ];
        $dateEn = (new \DateTime($date))->format('j F Y');
        $dateRu = $dateEn;
        foreach ($monthsEnRu as $monthsEn => $monthsRu) {
            if(preg_match("/$monthsEn/", $dateEn)) {
                $dateRu = preg_replace("/$monthsEn/", $monthsRu, $dateEn);
            }
        }

        return $dateRu;
    }


    /**
     * Возвращает валидную cтрочку для джаваскрипта либо false
     * Пример, подаём на вход (_shopId,76), получаем:
     * '_shopId':76
     * @param $key
     * @param $value
     * @return bool|string
     */
    public function stringRowParam4js($key,$value){
        $ret =false;
        $need_quotes = false;
        if ( isset($key) and !empty($key) /*and ($value)*/ ) { // Важно! пустое значение ( $value == "") НЕ будет игнориться
            $key = trim($key);
            $ret =  "'".$key."':";
            $value_str = (string) $value; // даже числа конвертнём в string для последующей конкатенации
            if ( is_string($value) ) {
                $value = (string) trim($value);
                $array_s = [ '{', '[', '"' , "'" ];
                if ( isset($value[0]) )
                if ( !in_array( $value[0], $array_s) ) {
                    $need_quotes = true;
                }

                if ( strlen($value)<3 ) {
                    $need_quotes = true;
                }
            }

            if ($need_quotes) {
                str_replace( "'" , '"' , $value_str); // заменяем одинарные кавычки на двойные
                $value_str = "'".$value_str."'"; // оборачиваем в одинарные кавычки, если string и не джаваскрипт-объект
            }

            if ( is_string($value_str) ){
                $ret .= $value_str;
            }else{
                $ret .= 'null';
            }
        }
        return $ret;
    }


    /**
     * Возвращает строчки ключей-параметров для JavaScript либо false
     * (предварительно делая провери и расставляя запятые и скобки).
     * в виде:
     * {
     * 'key1': 5,
     * 'key2': 'opop'
     * }
     * @param $params
     * @return bool|string
     */
    public function stringRowsParams4js($params){
        $ret =false;
        $count = count($params);
        if ($count>0){
            //$i = 0;
            $ret = (string) "{".PHP_EOL;
            $rows = [];
            foreach($params as $key => $value) {
                $rows[] = $this->stringRowParam4js($key,$value);
                /*$row = $this->stringRowParam4js($key,$value);
                if ($row) {
                    $i++;
                    $ret .= $row;
                    if ($i<$count) $ret .= ','.PHP_EOL;
                }else{
                    $count--;
                }*/
            }
            $ret .= implode(','.PHP_EOL , $rows);
            $ret .= PHP_EOL."}";
        }
        return $ret;

    }

}
