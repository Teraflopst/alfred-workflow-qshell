<?php
$dl_path = $argv[1];
$bucket_name = getenv('BucketName');
$bucket_dest_name = getenv('BucketDestName');
$file_name = getenv('FileName');
$file_name_q = '"'.$file_name.'"';
$file_rename = getenv('FileRename');
$file_rename_q = '"'.$file_rename.'"';
$file_action = getenv('FileAction');
$qshell_db = getenv('qshell_db');
$hr = "\n────────────────────────────────────────────────";
$cmd_domains_str = `cd ~/ && /usr/local/bin/qshell domains $bucket_name`; 
$domains_arr = preg_split('/[;\r\n]+/s', trim($cmd_domains_str));
$file_link = 'http://'.$domains_arr[0].'/'.$file_name;
$file_link = str_replace(" ","%20",$file_link);

if ($file_action == 'stat') {      //---------基本信息
    $cmd_stat_info = `cd ~/ && /usr/local/bin/qshell stat $bucket_name $file_name_q`;
    //echo $cmd_stat_info;
    $notify_center_title = '已获取基本信息！';
    $notify_blk_title = '基本信息（已复制到剪贴板）'.$hr.'───────────────────';
    $notify_blk_body = trim($cmd_stat_info);
} elseif ($file_action == 'delete') {     //---------删除
    $cmd_delete_info = `cd ~/ && /usr/local/bin/qshell delete $bucket_name $file_name_q`;
    if (strpos($cmd_delete_info,"done")!== false){
        $notify_center_title = "✅ 删除成功！";
        $notify_blk_title = "✅ 删除成功！".$hr;
        $notify_blk_body =  '删除文件 ['.$bucket_name.'] '.$file_name_q;
    } else {
        $notify_center_title = "⚠️ 删除失败！";
    }
} elseif ($file_action == 'link') {   //---------外链
    $notify_center_title = '外链已复制！';
    $notify_blk_title = '外链（已复制到剪贴板）'.$hr;
    $notify_blk_body = $file_link;
} elseif ($file_action == 'preview') {     //---------预览
    if (strpos($file_name,"/")!== false) {
        $cmd_curl_output = `cd $qshell_db; curl -O "$file_link"`;
        echo basename($file_name);
    } else {
        $cmd_curl_output = `cd $qshell_db; curl -o "$file_name" "$file_link"`;
        echo $file_name;
    }
} elseif ($file_action == 'download') {     //---------下载
    $bucket_items_str = `cat $qshell_db/listbucket.txt`;
    $bucket_items_arr = preg_split('/[;\r\n]+/s', trim($bucket_items_str));
    foreach ($bucket_items_arr as $bucket_item) {
        $bucket_item_stat_arr = explode("\t",$bucket_item);
        if ( $bucket_item_stat_arr[0] == $file_name ) {
            $stat_fsize = $bucket_item_stat_arr[1];
            $dl_path_q = '"'.$dl_path.'"';
            if (strpos($file_name,"/")!== false) {
                $cmd_curl_output = `cd $dl_path_q; curl -O -w %{size_download} "$file_link"`;
            } else {
                $cmd_curl_output = `cd $dl_path_q; curl -o "$file_name" -w %{size_download} "$file_link"`;   
            }
            if (($cmd_curl_output/$stat_fsize)==1) {
                $notify_center_title = '✅ 下载成功！';
                $notify_blk_title = '✅ 下载成功！（路径已复制到剪贴板）'.$hr;
                $notify_blk_body = $dl_path.basename($file_name);
            } else { 
                $notify_center_title = '⚠️ 下载未完成！';
                $notify_blk_title = '⚠️ 下载未完成！'.$hr;
                $notify_blk_body = '请查看剪贴板中文件夹路径'.$dl_path;
            }
            break;
        }
    }
} elseif (($file_action == 'copy') && (trim($file_rename) != "")) {    //---------复制
    $cmd_copy_str = `cd ~/ && /usr/local/bin/qshell copy $bucket_name $file_name_q $bucket_dest_name $file_rename_q`;
    if (strpos($cmd_copy_str,"done")!== false) {
        $notify_center_title = "✅ 复制成功！";
        $notify_blk_title =  "✅ 复制成功！".$hr;
        $notify_blk_body =  '复制文件  ['. $bucket_name.'] '.$file_name_q."\n到达空间  [". $bucket_dest_name."]\n".'重命名为  '.$file_rename_q;
    } elseif (strpos($cmd_copy_str,"exists")!== false) {
        $notify_center_title = "⚠️ 复制失败！";
        $notify_blk_title = "⚠️ 复制失败！".$hr;
        $notify_blk_body =  '错误: ['. $bucket_dest_name.'] 中已存在文件 '.$file_rename_q;
    } else {
        echo '⚠️ 复制失败！';
    }
} elseif (($file_action == 'move' || $file_action == 'rename') && (trim($file_rename) != "")) {    //---------重命名、移动
    $cmd_copy_str = `cd ~/ && /usr/local/bin/qshell move $bucket_name $file_name_q $bucket_dest_name $file_rename_q`;
    if ($bucket_name == $bucket_dest_name) {
        if (strpos($cmd_copy_str,"done")!== false) {
            $notify_center_title = "✅ 重命名成功！";
            $notify_blk_title =  "✅ 重命名成功！".$hr;
            $notify_blk_body =  '重命名文件 ['. $bucket_name.'] '.$file_name_q.' 为 '.$file_rename_q;
        } elseif (strpos($cmd_copy_str,"exists")!== false) {
            $notify_center_title = "⚠️ 重命名失败！";
            $notify_blk_title = "⚠️ 重命名失败！".$hr;
            $notify_blk_body = '错误: ['. $bucket_name.'] 中已存在文件 '.$file_rename_q;
        } else {
            echo '⚠️ 重命名失败！';
        }
    } else {
        if (strpos($cmd_copy_str,"done")!== false) {
            $notify_center_title = "✅ 移动成功！";
            $notify_blk_title = "✅ 移动成功！".$hr;
            $notify_blk_body = '移动文件  ['. $bucket_name.'] '.$file_name_q."\n到达空间  [". $bucket_dest_name."]\n".'重命名为  '.$file_rename_q;
        } elseif (strpos($cmd_copy_str,"exists")!== false) {
            $notify_center_title = "⚠️ 移动失败！";
            $notify_blk_title = "⚠️ 移动失败！".$hr;
            $notify_blk_body = '错误: ['. $bucket_name.'] 中已存在文件 '.$file_rename_q;
        } else {
            echo '⚠️ 移动失败！';
        }
    }
} else {
    echo '⚠️ 出现错误！';
}

echo $notify_center_title,"\n@@blk-t@@\n",$notify_blk_title,"\n@@blk-b@@\n",$notify_blk_body;

?>