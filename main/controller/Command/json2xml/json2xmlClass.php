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
    private $log_format = 'console';
    private $coreUrl= 'http://tester.core.ent3.ru/v2/';


    function __construct( $config ) {
        if (!defined("MAX_IERATIONS")){ // Максимальное кол-во строчек фалйа, которое будет прочитано (проходов цикла)
            define("MAX_IERATIONS", -1);  // При отрицательном значении обходит все строки файла
        }


        // Config Begin
        $this->input_json_file = $config['input_json_file'];
        $this->output_xml_file = $config['output_xml_file'];

        $this->coreUrl = $config['coreUrl'] ?: $this->coreUrl;
        $this->log_format = $config['log_format'] ?: $this->log_format;
        $this->log_file_name = $config['log_file_name'] ?: $this->log_file_name;
        // /Config End


        if ( file_exists( $this->log_file_name ) ) unlink( $this->log_file_name ); // Delete log file
        $this->log_file = fopen($this->log_file_name, "w");
    }

    function __destruct() {
        fclose( $this->log_file );
    }


    function file_log( $var ) {
        fputs( $this->log_file,  "$var" );
    }


    private function echlog($var, $desc = null)
    {
        $res = '';

        if (!empty($desc)) {
            $desc =  $desc . ':   ';
        }

        if (is_string($var)) $res .= '### ' . $desc;
        $res .= print_r($var, 1);

        if ( $this->log_format != 'console' ) {
            $res = '<pre>' . $res . '</pre>' . "\n";
        }
        $res .= PHP_EOL;

        echo $res;
    }


    private function loadCategory($id) {
        $core_method = 'category/get';
        $get_params = '?' . urlencode('id') . '=' . $id;

        $core_url = $this->coreUrl . $core_method . $get_params;
        $response = $this->curl($core_url);

        return isset($response->result) ? $response->result : false;
    }







    public function execute()
    {
        //$categoryInf = $this->loadCategory(1);


        $time_start = time();
        $this->echlog( 'Время начала: ' . $time_start );



        // config //old
        //$json_path = '../../../cms.enter.ru/';
        //$json_filename = "product-export.json";
        //$xml_filename = 'market.xml';
        //$json_filename = $json_path . $json_filename;


        // config!
        $json_filename  = $this->input_json_file;
        $xml_filename   = $this->output_xml_file;



        if (file_exists($json_filename)) {
            $this->echlog('Файл ' . $json_filename .' открыт');
        } else {
            $this->echlog('Файл ' . $json_filename . ' не найден');
            $this->echlog('Завершаем выполнение');
            return false;
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


        $categories = $xml->shop->addChild('categories');
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
                        $cid = $cat->category_id;

                        // наполняем массив всех-всех-всех категорий
                        if ( !isset($categories_arr[$cid]) ) {
                            $categories_arr[$cid] = $cat;
                        }

                        // Находим главную категорию товара и выходим
                        if ($cat->is_main) {
                            $categoryId = $this->addIfIsset( $cid );
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





                $progressbar = 'Is Readed ' . $readed . ' from '. $file_size. '; '. round( ($readed/$file_size)*100 , 2 ) . '%' ;

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


            } // END IF ISSET( $json_line->id)


        } // end while


        if ( !empty($categories_arr) && is_array($categories_arr) ) {
            foreach ( $categories_arr as $cat) {
                $paretn_id = 0;
                $arr = explode(',', $cat->path);

                if ( $cat->category_id == end($arr) ) {
                    $paretn_id = prev($arr);
                }

                $category = $categories->addChild('category', $cat->name);
                $category->addAttribute('id', $cat->category_id);
                if ($paretn_id) $category->addAttribute('parent_id', $paretn_id);
                //$category->addAttribute('url', $cat->category_id); // <-- TODO
            }
        }


        // считаем, что дубликатов товаров в исходном файле нет

        if ($json_file) {
            fclose($json_file);
            $this->echlog('Файл ' . $json_filename .' закрыт');
        }



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



    private function curl($url, $post_arr = []) {
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_arr );
            $out = curl_exec($curl);
            curl_close($curl);
            return json_decode($out);
        }
        return false;
    }

}