<?php
session_start();
include_once('conexao.php');
$username = $_POST['username'];
$password = $_POST['password'];
$nivel_acesso = 0;

// Teste de login user/admin
if (substr($username, 0, 1) === '@') {
    // If the first letter of the username is "@"
    $sql = "SELECT * FROM tblusers WHERE NameUser LIKE '$username' AND PasswordUser LIKE '$password' AND nivel_acesso = 1";
    $result = $connection->query($sql);
    if (mysqli_num_rows($result) < 1) {
        unset($_SESSION['username']);
        unset($_SESSION['password']);
        header('location: login.html');
    } else {
        $nivel_acesso = 1;
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        $_SESSION['nivel_acesso'] = $nivel_acesso;
        header('Location: indexAdmin.html');

    }

} else {
    $sql = "SELECT * FROM tblusers WHERE NameUser LIKE '$username' AND PasswordUser LIKE '$password' AND nivel_acesso = 0";
    $result = $connection->query($sql);

    if (mysqli_num_rows($result) < 1) {
        unset($_SESSION['username']);
        unset($_SESSION['password']);
        header('location: login.html');
    } else {
        $nivel_acesso = 0;
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        $_SESSION['nivel_acesso'] = $nivel_acesso;
        header('Location: indexUser.php');

    }
}

