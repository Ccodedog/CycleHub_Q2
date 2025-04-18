<?php

$sname = "sql309.infinityfree.com";
$uname = "if0_38757269";
$password = "C20040326cc";
$db_name = "if0_38757269_cyclehub_db";
$conn = mysqli_connect($sname, $uname, $password, $db_name);
if(!$conn) {
    echo "Connection failed!";
    die(mysqli_connect_error());
}