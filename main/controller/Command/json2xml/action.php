<pre>
<?

// config
$json_path = '../../../cms.enter.ru/';
$json_filename = "product-export.json";
$xml_filename = 'market.xml';



//
$json_filename = $json_path . $json_filename;


if ( file_exists($json_filename) ) {
    echo 'файл открыт';
}else{
    echo 'файл ' . $json_filename . ' не найден';
}


$json_file = fopen($json_filename, "r");
$i = 0;

$xml = new SimpleXMLElement( '<?xml version="1.0" encoding="utf-8" ?><xml_catalog></xml_catalog>' );


$xml->addChild('shop');
$xml->shop->addChild('offers');
$xml->shop->offers->addChild('offer');

$offers = &$xml->shop->offers;
//$offer = &$xml->shop->offers->addChild('offer');



while ( !feof($json_file) and $i<2 ) {
    $i++;
    $buffer = fgets($json_file);
    $json_line = json_decode( $buffer );

    $params = [];

    $params['id'] = $json_line->id;
    $params['link'] = $json_line->link;
    $params['media_image'] = $json_line->media_image;
    $params['price'] = $json_line->geo->{1}->price;
    $params['vendor'] = $json_line->brand->name ?: $json_line->name_web;
    $params['description'] = $json_line->description;

    foreach ( $json_line->categories as $cat ) {
        // Находим главную категорию товара и выходим
        if ( $cat->is_main ) {
            $categoryId = $cat->category_id;
            break;
        }
    }

    $offers->addChild( 'offer' );
    $offer = $offers->offer;

    $offer->addAttribute('id', $id);
    $offer->addAttribute('available', 'true');

    foreach( $params as $name => $value ) {
        $offer->addChild( $name , $value );
    }

    print_r($json_line);
    //print_r($json_line->link);
}

//есть ли дубликаты товаров в исходном файле?

fclose($json_file);




// Add stuff to it
//$xml->option1->addAttribute( 'first_name', 'billy' );
//$xml->option1->addAttribute( 'middle_name', 'bob' );
//$xml->option1->addAttribute( 'last_name', 'thornton' );
//$xml->addChild( 'option2' );
//$xml->option2->addAttribute( 'fav_dessert', 'cookies' );

// Save
if ( file_exists($xml_filename) ) {
    unlink($xml_filename);
}
$xml->asXML( $xml_filename );

//echo htmlspecialchars($xml->asXML());


?>
</pre>