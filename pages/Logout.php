<?php 
/* Copyright (c) 2024 Mohammad J Hamdar */
// This Page is responsible for logging out a user from the web application.
session_start();

if(isset($_SESSION["username"])) {
   
    session_destroy();
    header('Location: Login.php');
    exit(); 
}
?>