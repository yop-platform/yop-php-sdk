<?php

print_r($_FILES);
$uploaddir = getcwd() . '/tmp/'; //a directory inside
echo $uploaddir . "<br />";
echo $_FILES["fff"]["name"] . "<br />";
$file_name = basename($_FILES["fff"]["name"]);
echo $file_name . "<br />";
move_uploaded_file($_FILES['fff']['tmp_name'], $uploaddir . $file_name);


//测试是否有写入权限
//$fp=fopen("tmp.txt","wb");
//fwrite($fp,"abc\r\n");
//fclose($fp);