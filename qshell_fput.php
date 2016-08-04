<?php

$fput_type = getenv('FputType');
$bucket_name = getenv('BucketName');
$file_rename = getenv('FileRename');
$prefix = getenv('Prefix');
$overwrite = getenv('Overwrite');

$local_file_paths = getenv('LocalFilePaths');
$local_file_paths_arr = explode("\t",$local_file_paths);
$local_file_paths_count = count($local_file_paths_arr);

$file_count = 0;
$dirs_count = 0;
$dirs_name = '';
$file_size_all_b = 0;
$file_size_success_all_b = 0;
$notify_large_text_main = '';
$file_link = '';
$domains_str = `cd ~/ && /usr/local/bin/qshell domains $bucket_name`; 
$domains_arr = preg_split('/[;\r\n]+/s', trim($domains_str));

function fileSizeConv($fsize_b) {
    if ($fsize_b < 1048576) {
        return sprintf("%.2f", $fsize_b/1024)." KB";
    } else {
        return sprintf("%.2f", $fsize_b/1048576)." MB";
    }
}
foreach ($local_file_paths_arr as $key => $file_path) {
    $file_path_q = '"'.$file_path.'"';
    $path_type_info = `file -b $file_path_q`;
    if (trim($path_type_info) != "directory") {
        $file_count += 1;
        $file_size_b = `stat -f%z $file_path_q`;
        $file_size_all_b += $file_size_b;
        $file_size = fileSizeConv($file_size_b); 
        if (trim($file_rename) == "") {
            echo "\n@@big@@\n","⚠️ 文件名不能全为空格！","\n@@link@@\n";
            exit();
        } elseif ($file_rename == "...") {
            $file_new_name = $prefix.basename($file_path);
        } else {
            $file_new_name = $prefix.$file_rename;
        }
        $file_new_name_q = '"'.$file_new_name.'"';

        $qshell_fput_info = `cd ~/ && /usr/local/bin/qshell fput $bucket_name $file_new_name_q $file_path_q $overwrite`;

        if (strpos($qshell_fput_info,"success") !== false) {
            $fput_info_strstr = strstr($qshell_fput_info, 'Last time:');
            $fput_info_arr = explode(", ",$fput_info_strstr);
            $time = str_replace("Last time: ","⌛️ ",$fput_info_arr[0]);
            $speed = str_replace("Average Speed: ","🚀 ",trim($fput_info_arr[1]));
            $file_link .= ('http://'.$domains_arr[0].'/'.str_replace(" ","%20",$file_new_name)."\n");
            $notify_large_text_main .= ("✅  ".$file_new_name_q.":  成功！  📦 ".$file_size."    ".$time."    ".$speed."\n");
            $file_size_success_all_b += $file_size_b;
        } elseif (strpos($qshell_fput_info,"exists")!== false) {
            $notify_large_text_main .= ("⚠️  ".$file_new_name_q.":  失败！  大小: ".$file_size.'  错误: 文件已存在。可尝试覆盖上传。'."\n");
        } elseif (strpos($qshell_fput_info,"no such file")!== false) {
            $notify_large_text_main .= ("⚠️  ".$file_new_name_q.":  失败！  错误: 未找到本地文件"."\n");
        } elseif (strpos($qshell_fput_info,"no such bucket")!== false) {
            $notify_large_text_main .= ("⚠️  ".$file_new_name_q.":  失败！  错误: 未找到此空间"."\n");
        } elseif (strpos($qshell_fput_info,"Usage:")!== false) {
            $notify_large_text_main .= ("⚠️  ".$file_new_name_q.":  失败！  错误: 命令错误"."\n");
        } else {
            $notify_large_text_main .= ("⚠️  ".$file_new_name_q.":  失败！  错误: 未知"."\n");
        }
    } elseif (trim($path_type_info) == "directory") {
        $dirs_count += 1;
        $dirs_name .= basename($file_path)."/,  ";
    }
}

$fput_not_support_count = $local_file_paths_count - $file_count;
$fput_success_count = substr_count($notify_large_text_main, '成功！');
$fput_fail_count = substr_count($notify_large_text_main, '失败！');
$file_size_all = fileSizeConv($file_size_all_b);
$file_size_success_all = fileSizeConv($file_size_success_all_b);
$hr = "\n─────────────────────────────────────────────────────────────────────\n";
$notify_large_text_title = '上传队列: '.$file_count.'，✅ 成功: '.$fput_success_count.'，⚠️ 失败: '.$fput_fail_count.'，⛔️ 不支持: '.$fput_not_support_count."，📦 大小: ".$file_size_success_all."/".$file_size_all."  （链接已复制到剪贴板）";
$notify_large_text_sub = "⛔️  不支持上传格式\n"."📂  文件夹 (".$dirs_count.") : ".substr_replace($dirs_name, '', -3)."\n";
$notify_large_text = $notify_large_text_title.$hr.$notify_large_text_main."\n".$notify_large_text_sub;
$notify_center = "上传：✅".$fput_success_count."   ⚠️".$fput_fail_count."   📦".$file_size_success_all;

echo $notify_center,"\n@@big@@\n",$notify_large_text,"\n@@link@@\n",$file_link;

?>