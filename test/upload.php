<?php
/**
 * User: yp-tc-7176
 * Date: 17/8/5
 * Time: 13:59
 */
// Path to the file we want to upload

echo exec('whoami');

$uploaddir = getcwd();
$file = $uploaddir . "/1.jpeg"; //这里非常重要！一定要绝对地址才行，所以使用这个拼接成了绝对地址
echo $file;

echo $file;

// Create a cURL handle
$ch = curl_init('http://localhost/yop/test/up2.php');


// Create a CURLFile object
$cfile = curl_file_create($file);


// Assign POST data
$data = array('fff' => $cfile);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file)); //这句非常重要，告诉远程服务器，文件大小，查到的是前辈的文章http://blog.csdn.net/cyuyan112233/article/details/21015351


// Execute the handle
curl_exec($ch);
