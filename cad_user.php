<?php
include_once('conexao.php');
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];


print_r("<BR>" . $email);
print_r("<BR>" . $username);
print_r("<BR>" . $password);

$result = mysqli_query($connection, "SELECT * FROM tblusers WHERE NameUser LIKE '$username'");

print_r("<BR>");
print_r($result);

if (isset($_POST['submit']))
    ;
if (mysqli_num_rows($result) > 0) {
    // header("Location: cadUser.html");
    exit; // Interrompe a execução do restante do script
    // echo "<center><br>USUÁRIO JÁ CADASTRADO!!!<br></center>";
} else {
    // print_r($result);
    // echo "<center><br>USUÁRIO CADASTRADO!!!<br></center>";
    $result = mysqli_query($connection, query: "INSERT INTO `tblusers` (`idUser`, `NameUser`, `PasswordUser`, `EmailUser`) 
    VALUES (NULL, '$username', '$password', '$email');");
    print_r($result);
    // header('Location: index.html.html');
    exit; // Interrompe a execução do restante do script
}

?>