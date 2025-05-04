<?php
    session_start();
    include_once("conexao.php");
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    header('location: index.html');
    session_destroy();
    exit;
?>