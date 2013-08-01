<?php
/**
 * Created by JetBrains PhpStorm.
 * User: juljan
 * Date: 1.8.13
 * Time: 16.00
 * To change this template use File | Settings | File Templates.
 */

namespace Controller\Command;


class json2xml
{
    private $log_file_name = 'json2xml_log.txt';
    private $log_file;
    private $log_format = 'console'
    ;


    function __construct() {
        define("MAX_IERATIONS", 500);
        if ( file_exists( $this->log_file_name ) ) unlink( $this->log_file_name );
        $this->log_file = fopen($this->log_file_name, "w");
    }

    function __destruct() {
        fclose( $this->log_file );
    }


    function file_log( $var ) {
        fputs( $this->log_file,  "$var" );
    }


    private function echlog($var)
    {
        $res = '';

        if (is_string($var)) $res .= '### ';
        $res .= print_r($var, 1);

        if ( $this->log_format != 'console' ) {
            $res = '<pre>' . $res . '</pre>' . "\n";
        }
        $res .= PHP_EOL;

        echo $res;
    }





    public function execute()
    {

        $time_start = time();
        self::echlog( 'Время начала: ' . $time_start );

        // config
        $json_path = '../../../cms.enter.ru/';
        $json_filename = "product-export.json";
        $xml_filename = 'market.xml';

        $json_filename = $json_path . $json_filename;


        if (file_exists($json_filename)) {
            self::echlog('Файл ' . $json_filename .' открыт');
        } else {
            self::echlog('Файл ' . $json_filename . ' не найден');
        }
        $json_file = fopen($json_filename, "r");


        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><xml_catalog></xml_catalog>');
        $xml->addChild('shop');
        $offers = $xml->shop->addChild('offers');

        $file_size = filesize($json_filename);

        $i = 0;
        $readed = 0;
        while ( !feof($json_file) && (MAX_IERATIONS<0 || $i < MAX_IERATIONS) ) {
            $i++;
            $buffer = fgets($json_file);
            $buf_size = strlen($buffer);
            $readed += $buf_size;
            $json_line = json_decode($buffer);


            IF ( ISSET( $json_line->id ) ) { // у каждого товара должен быть ID, если нету, видимо пустая строка в файле

                $params = [];

                $params['id'] = $json_line->id;
                $params['link'] = self::addIfIsset( $json_line->link );
                $params['media_image'] = self::addIfIsset( $json_line->media_image );

                $params['price'] = self::addOneOfElems(
                    self::addIfIsset( $json_line->geo),
                    'price'
                );

                $arr = [];
                if ( isset( $json_line->brand->name ) )
                    $arr[] = self::addIfIsset( $json_line->brand->name );

                if ( isset( $json_line->name_web ) )
                    $arr[] = self::addIfIsset( $json_line->name_web );

                $params['vendor'] = self::addOneOfElems( $arr );

                $params['description'] = self::addIfIsset( $json_line->description );
                //$params['price'] = $json_line->geo->{1}->price; // old variant, price in Moscow


                foreach ($json_line->categories as $cat) {
                    // Находим главную категорию товара и выходим
                    if ($cat->is_main) {
                        $categoryId = $cat->category_id;
                        break;
                    }
                }


                $progressbar = ' Readed ' . $readed . ' from '. $file_size. '; '. round( ($readed/$file_size)*100 , 2 ) . '%' ;


                //self::echlog($json_line); // log // all product-info FROM JSON file


                //self::echlog ( 'ProductID ' . $params['id'] . '; ' . $progressbar  );
                //self::file_log ( 'ProductID ' . $params['id'] . '; ' . $progressbar  );

                //self::echlog( $params ); // log // all product-info FOR XML file





                $offer = $offers->addChild('offer');
                $offer->addAttribute('id', $params['id']);
                $offer->addAttribute('available', 'true');

                foreach ($params as $name => $value) {
                    //$offer->addChild($name, $value); // achtung! witch warnings!
                    $offer->{$name} = $value; // ok! without warnings
                } // end foreach


            } // END IF


        } // end while


        //TDo?есть ли дубликаты товаров в исходном файле?


        fclose($json_file);
        self::echlog('Файл ' . $json_filename .' закрыт');


        // Save
        if (file_exists($xml_filename)) unlink($xml_filename);
        $return = $xml->asXML($xml_filename);
        $xml = null;
        self::echlog('Файл ' . $xml_filename .' сохранён');


        $time_end = time();
        self::echlog( 'Время выполнения: ' . self::timeFromSeconds($time_end - $time_start) );
        self::echlog( 'Время окончания: ' . $time_end );


        return $return;
    }



    private function addIfIsset( &$var ) {
        if ( isset($var) ) return $var;
        return false;
    }



    private function addOneOfElems ( $arr, $attrib = null ) {

        foreach ( $arr as $elem ) {

            if ( !$attrib ) $ret = self::addIfIsset( $elem );
                else $ret = self::addIfIsset( $elem->{$attrib} );

            if ( $ret ) return $ret;

        }

        return false;
    }


    protected function timeFromSeconds($time) {
        $m = $h = null;
        $s = (int) $time;
        if ( $s > 60 ) {
            $m = (int) ( $s / 60 );
            $s = $s % 60;
            if ( $m > 60 )  {
                $h = (int) ( $m / 60 );
                $m = $m % 60;
            }
        }

        $ret = "$s сек";
        if ( $m ) {
            $ret = "$m мин " . $ret;
            if ( $h ) {
                $ret = "$h ч " . $ret;
            }
        }

        if ( $this->log_format != 'console' ) $ret = '<time>' . $ret. '</time>';

        return $ret;
    }



}