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
    private $log_format //= 'console'
    ;


    function __construct() {
        //define("MAX_IERATIONS", -1); // for all
        define("MAX_IERATIONS", 3); // for test
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
        $this->echlog( 'Время начала: ' . $time_start );

        // config
        $json_path = '../../../cms.enter.ru/';
        $json_filename = "product-export.json";
        $xml_filename = 'market.xml';

        $json_filename = $json_path . $json_filename;


        if (file_exists($json_filename)) {
            $this->echlog('Файл ' . $json_filename .' открыт');
        } else {
            $this->echlog('Файл ' . $json_filename . ' не найден');
        }
        $json_file = fopen($json_filename, "r");
        $file_size = filesize($json_filename);


        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><!DOCTYPE yml_catalog SYSTEM "shops.dtd"><yml_catalog></yml_catalog>');
        $shop = $xml->addChild('shop');


        /** head **/
        $shop->addChild('name', 'Enter.ru');
        $shop->addChild('company', 'Enter.ru');
        $shop->addChild('url', 'http://www.enter.ru');
        $shop->addChild('email', 'enter@enter.ru');

        $currencies = $shop->addChild('currencies');
        $currency = $currencies->addChild('currency');
        $currency->addAttribute('id', 'RUR');
        $currency->addAttribute('rate', '1');
        /** /head **/


        $offers = $xml->shop->addChild('offers');


        $i = 0;
        $readed = 0;
        while ( !feof($json_file) && (MAX_IERATIONS<0 || $i < MAX_IERATIONS) ) {
            $i++;
            $buffer = fgets($json_file);
            $buf_size = strlen($buffer);
            $readed += $buf_size;
            $json_line = json_decode($buffer);


            IF ( ISSET( $json_line->id ) ) { // у каждого товара должен быть ID, если нету, видимо пустая строка в файле

                $id = $json_line->id;

                $categoryId = 0;
                if ( isset($json_line->categories) )
                    foreach ( $json_line->categories as $cat ) {
                        // Находим главную категорию товара и выходим
                        if ($cat->is_main) {
                            $categoryId = $this->addIfIsset( $cat->category_id );
                            break;
                        }
                    }

                /////////////////////////////////

                $vendor_arr = [];
                if ( isset( $json_line->brand->name ) )
                    $vendor_arr[] = $this->addIfIsset( $json_line->brand->name );

                if ( isset( $json_line->name_web ) )
                    $vendor_arr[] = $this->addIfIsset( $json_line->name_web );

                /////////////////////////////////


                $params = [];

                /** обязательные параметры **/
                //$params['price'] = $json_line->geo->{1}->price; // old variant, price in Moscow
                $params['price'] = $this->addOneOfElems( $this->addIfIsset( $json_line->geo), 'price' );
                $params['url'] = $this->addIfIsset( $json_line->link );
                $params['picture'] = $this->addIfIsset( $json_line->media_image );
                $params['vendor'] = $this->addOneOfElems( $vendor_arr );
                $params['category_id'] = $categoryId;
                $params['currency_id'] = $id;
                /** /обязательные параметры **/


                /** желательные параметры **/
                $params['oldprice'] = $this->addIfIsset( $json_line->old_price ); // !isset for all?
                $params['description'] = $this->addIfIsset( $json_line->description );
                $params['typePrefix'] = $this->addIfIsset( $json_line->type_id );
                $params['model'] = $this->addIfIsset( $json_line->model_id );
                $params['vendorCode'] = $this->addOneOfElems( [$json_line->bar_code, $json_line->name_web] );
                /** /желательные параметры **/






                $progressbar = ' Readed ' . $readed . ' from '. $file_size. '; '. round( ($readed/$file_size)*100 , 2 ) . '%' ;

                //$this->echlog($json_line); // log // all product-info FROM JSON file
                //$this->echlog ( 'ProductID ' . $params['id'] . '; ' . $progressbar  );
                //self::file_log ( 'ProductID ' . $params['id'] . '; ' . $progressbar  );
                //$this->echlog( $params ); // log // all product-info FOR XML file




                $offer = $offers->addChild('offer');
                $offer->addAttribute('id', $id);
                $offer->addAttribute('available', 'true');

                foreach ($params as $name => $value) {
                    //$offer->addChild($name, $value); // achtung! witch warnings!
                    if ( !empty($value) ) $offer->{$name} = $value; // ok! without warnings
                } // end foreach


            } // END IF


        } // end while


        //TDo?есть ли дубликаты товаров в исходном файле?


        fclose($json_file);
        $this->echlog('Файл ' . $json_filename .' закрыт');


        // Save
        if (file_exists($xml_filename)) unlink($xml_filename);
        $return = $xml->asXML($xml_filename);
        $xml = null;
        $this->echlog('Файл ' . $xml_filename .' сохранён');


        $time_end = time();
        $this->echlog( 'Время выполнения: ' . self::timeFromSeconds($time_end - $time_start) );
        $this->echlog( 'Время окончания: ' . $time_end );


        return $return;
    }



    private function addIfIsset( &$var ) {
        if ( isset($var) ) return $var;
        return false;
    }



    private function addOneOfElems ( $arr, $attrib = null ) {

        foreach ( $arr as $elem ) {

            if ( !$attrib ) $ret = $this->addIfIsset( $elem );
                else $ret = $this->addIfIsset( $elem->{$attrib} );

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