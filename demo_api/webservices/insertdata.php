<?php
mysql_connect("localhost","root","");
mysql_select_db("test");
$id=$_GET['id'];
$name=$_GET['name'];
$age=$_GET['age'];
if(mysql_query("insert into `insertmyurldata` set id='$id',name='$name',age='$age'")){
    return TRUE;
}else{
    return FALSE;
}
