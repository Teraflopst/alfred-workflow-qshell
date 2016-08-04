<?php
date_default_timezone_set("PRC");
$BucketName = getenv('BucketName');
$qshell_db = getenv('qshell_db');
`mkdir -p $qshell_db`;  //Create a data folder

$listbucket_path = $qshell_db.'/listbucket.txt';
$cmd_listbucket = `cd ~/ && /usr/local/bin/qshell listbucket $BucketName $listbucket_path`;
$cmd_domains = `cd ~/ && /usr/local/bin/qshell domains $BucketName`; 

$domains_arr = preg_split('/[;\r\n]+/s', trim($cmd_domains));
$cat_listbucket = `cat $listbucket_path`;
$buckets_arr = preg_split('/[;\r\n]+/s', trim($cat_listbucket));
$json_item = "";
foreach ($buckets_arr as $bucket_item) {
    $bucket_item_stat_arr = explode("\t",$bucket_item); 
    $stat_fsize = sprintf("%.2f", (int)$bucket_item_stat_arr[1]/1024)." KB";
    $stat_puttime = date('Y-m-d H:i:s',substr($bucket_item_stat_arr[3],0,-7));
    //script filter json
    $uid = '"uid":'.'"'.$bucket_item_stat_arr[0].'",';
    $title = '"title":'.'"'.$bucket_item_stat_arr[0].'",';
    $subtitle = '"subtitle":'.'"['.$BucketName.']  size: '.$stat_fsize.', up: '.$stat_puttime.' 【预览: ⇧ 或 ⌘+Y】'.'",';
    $arg = '"arg":'.'"'.$bucket_item_stat_arr[0].'",';
    $ql = '"quicklookurl": "http://'.$domains_arr[0].'/'.$bucket_item_stat_arr[0].'",';
    $json_item .= '{'.$uid.$title.$subtitle.$arg.$ql.'},';
}
$json_str = '{"items":['.$json_item.']}';
echo $json_str;

?>