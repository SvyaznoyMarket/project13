<?php
$link = mysql_connect("10.20.33.2", "mon", "");
if (!$link)
{
    echo 'fail';
    exit(1);
}
$res = mysql_query("select 'ok';");
if ($res)
{
    $row = mysql_fetch_row($res);
    echo($row[0]);
    mysql_free_result($res);
}
else
{
    echo 'fail';
}
mysql_close($link);
?>
