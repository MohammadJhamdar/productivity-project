<?php
/* Copyright (c) 2024 Mohammad J Hamdar */
//Connection To MySQL Database
$server="localhost";
$user="root";
$pass="";
$db= "phpproject";
$conn=mysqli_connect($server,$user,$pass,$db);
if(mysqli_connect_errno()){
    echo "".mysqli_connect_error();
    exit();
}

?>