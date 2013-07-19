<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
$API_ulr = 'http://api.livetex.ru/';
$API_login_ulr = 'http://api.livetex.ru/login.php';

$login = 'anastasiya.vs@enter.ru';
$password = 'enter1chat2';

$authKey = '';
$chief_id = 0;



if( $curl = curl_init() ) {
    curl_setopt($curl, CURLOPT_URL, $API_login_ulr);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS,
        [
            'login' => $login,
            'password' => $password,
        ]
    );
    $out = curl_exec($curl);
    //echo $out;
    curl_close($curl);
}

$json = json_decode($out);
$authKey = $json->response->authkey;
$chief_id = $json->response->chief_id;




if( $curl = curl_init() ) {
    curl_setopt($curl, CURLOPT_URL, $API_login_ulr);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS,
        [
            'chief_id' => $login,
            'password' => $password,
        ]
    );
    $out = curl_exec($curl);
    //echo $out;
    curl_close($curl);
}
*/

include_once '../lib/LiveTex/Statistics.php';
$API = \LiveTex\Api::getInstance();
$resp = $API->login();
/*
$resp = $API->method('Operator.ChatStat', [
    'date_from' => '2013-03-01',
    'date_end' => '2013-07-18',
    'id' => '66252'
]);*/

//$resp = $API->method('Operator.GetList');

print_r($resp);



?>
<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="ru-RU" xml:lang="ru-RU">
<head>
    <script src="http://yandex.st/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
    <script type='text/javascript'>
        var LiveTexStat = {
            haveOnline: false,
            load: function () {
                console.log("LiveTexStat успешно инициализировано");
                LiveTexStat.haveOnline();
                LiveTexStat.getOperators();
            },
            haveOnline: function() {
                LiveTex.haveOnlineOperators(function(data) {
                    $("#haveOnline .online").show();
                    LiveTexStat.haveOnline = true;
                }, function() {
                    $("#haveOnline .offline").show();
                    LiveTexStat.haveOnline = false;
                });
            },
            getOperators :function() {
                if ( LiveTexStat.haveOnline ) {
                    LiveTex.getOperators( function( data ) {
                        //console.log(data);
                        for (var key in data) {
                            var opers = [];

                            opers['ava'] = $('<img>', { src: data[key].avatar, class: "img_ava" });
                            opers['ava'] = $('<div>', { html: opers['ava'], class: "ava_oper" });

                            opers['name'] = $('<div>', { html: '<span class="param_name">Имя: </span>' + data[key].firstname + ' ' + data[key].lastname, class: "name_oper" });

                            opers['id'] = $('<div>', { html: '<span class="param_name">ID: </span>' + data[key].id, class: "id_oper" });

                            opers['departments'] = $('<div>', { html: '<span class="param_name">Departments: </span>' + data[key].department_id.join( ', ' ), class: "depart_oper" });

                            opers['state_id'] = $('<div>', { html: '<span class="param_name">State_id: </span>' + data[key].state_id, class: "state_oper" });

                            opers['is_call'] = $('<div>', { html: '<span class="param_name">Call: </span>' + (data[key].is_call ? 'есть' : 'нет'), class: "iscall_oper" });


                            var op_li = $('<li>', {class: 'li_oper' });
                            for (var param in opers) {
                                op_li.append( opers[param] );
                            }
                            $('#operators').append( op_li );

                            //console.log(key);
                            //console.log(data[key]);
                        }
                        $('#count_opers').html( data.length );
                    } )
                } // end of LiveTexStat.haveOnline if
            }
        } // end of LiveTexStat Object
    </script>
    <style>
        .hidden {
            display: none;
        }

        .param_name {
            font-style: italic;
        }

        .img_ava {
            width: 50px;
            height: auto;
        }

        .ava_oper{
            float:left;
            margin: 0 10px 10px 0;
        }

        .li_oper{
            background: #e3e3e3;
            padding: .5em;
            border: 1px solid #ccc;
            margin: .5em;
            font-size: .85em;
        }

        .name_oper{
            font-size: 1.125em;
            font-weight: bold;
        }
    </style>
</head>


<body>
<noscript><p>Javascript must be enabled for the correct page display</p></noscript>
<div id="liveText_wr" class="liveTex_stat">
    <div id="haveOnline">
        <p class="online hidden">Найдены операторы онлайн: <span id="count_opers">0</span>.</p>
        <p class="offline hidden">Нет операторов онлайн</p>
    </div>
    <div id="operators_wr" class="operators_stat">
        <ul id="operators">

        </ul>
    </div>
</div>


<? /* LiveText initialization: */ ?>
<script>
    var LiveTex = {
        liveTexID: 41836,
        onLiveTexReady: function() {
            console.log("API LiveTex успешно инициализировано");
            LiveTexStat.load();
        }
    };

    (function() {
        var lt = document.createElement('script');
        lt.type = 'text/javascript';
        lt.async = true;
        lt.src = 'http://cs15.livetex.ru/js/api.js';
        var sc = document.getElementsByTagName('script')[0];
        if ( sc ) sc.parentNode.insertBefore(lt, sc);
        else  document.documentElement.firstChild.appendChild(lt);
    })();
</script>
</body>
</html>