<?php

$buckets_str = `cd ~/;/usr/local/bin/qshell buckets`;
$buckets = preg_split('/[;\r\n]+/s', trim($buckets_str));

$item = "";
foreach ($buckets as $value) {
  if ($value !== "") {
    $uid = '"uid":'.'"'.$value.'",';
    $title = '"title":'.'"'.$value.'",';
    $subtitle = '"subtitle":'.'"选择空间：'.$value.'",';
    $arg = '"arg":'.'"'.$value.'",';
    $icon = '"icon":{"path":"icon.png"}';
    $item .= '{'.$uid.$title.$subtitle.$arg.$icon.'},';
  }
}

$json_str = '{"items":['.$item.']}';
echo $json_str;

?>