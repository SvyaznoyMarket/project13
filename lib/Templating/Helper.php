<?php

namespace Templating;

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
                throw new \RuntimeException('В атрибутах запроса не задан параметр "route".');
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
    public function formatPrice($price) {
        return number_format($price, 0, ',', ' ');
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

}
