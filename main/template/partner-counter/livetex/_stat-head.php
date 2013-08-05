<?php ?>

<script type='text/javascript'>
    var LiveTexStat = {
        haveOnline: false,
        load: function () {
            console.log("LiveTexStat успешно инициализировано");
            //LiveTexStat.haveOnline();
            //LiveTexStat.getOperators();
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
    a {
        color: #00b !important;
    }

    .hidden {
        display: none;
    }

    .right{
        float:right;
    }

    .param_name {
        font-style: italic;
    }

    .img_ava {
        width: 45px;
        height: auto;
    }

    .ava_oper{
        float:left;
        margin: 0 10px 10px 0;
    }

    .lts_item{
        background: #e3e3e3;
        padding: .5em;
        border: 1px solid #ccc;
        margin: .5em;
        font-size: .85em;
    }

    .lts_name{
        font-size: 1.125em;
        font-weight: bold;
    }

    .lts_head{
        padding: .5em 0 0;
    }

    .error,
    .isonline{
        color:red;
    }

    .allpageinner {
        padding-bottom: 0 !important;
    }

    .infomess{
        margin: 16px 0 0;
        border: 1px solid #050;
        padding: 1em;
        background: #085;
        color: #fff;
    }

    .lts_analytics{
        margin-top: 16px;
        border: #CCC 1px solid;
        background: #f1f1f1;
    }

    .lts_item table{
        border:2px solid #bbb;
    }

    .lts_item th,
    .lts_item td{
        border:1px solid #ccc;
        padding:6px 8px;
    }

    .lts_item th{
        background: #333;
        color:#fff;
        font-weight: bold;
    }
</style>