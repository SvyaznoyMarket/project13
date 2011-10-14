<?

foreach($list as $item){
    echo '<a href="'.$item['url'].'" >'.$item['name'].'</a> > ';
}
?>
<?
if (has_slot('title')):
?>
<strong>
<?
    include_slot('title');
?>
</strong>
<?
endif;
?>