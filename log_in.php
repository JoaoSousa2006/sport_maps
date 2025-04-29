<?php
include_once('conexao.php');
$username = $_POST['username'];
$password = $_POST['password'];

// Teste de login
$sql = "SELECT * FROM tblusers WHERE NameUser LIKE '$username' AND PasswordUser LIKE '$password'";
$result = $connection->query($sql);

if (mysqli_num_rows($result) < 1) {
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    header('location: login.html');
} else {
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
    header('Location: index.html');

}
