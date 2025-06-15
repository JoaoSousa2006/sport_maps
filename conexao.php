<?php
// $servidor = "127.0.0.1"; 
// $usuario = "root"; 
// $senha = "usbw";
// $banco = "sport_maps";

$servidor = "localhost"; 
$usuario = "root"; 
$senha = "";
$banco = "sport_maps";

// Conectar ao banco de dados
$connection = mysqli_connect($servidor, $usuario, $senha, $banco);

// Verificar a conexão
if (!$connection) {
    die("Deu não :T " . mysqli_connect_error());
}
// Se conectar com sucesso
else{
    // echo "Conexao bem-sucedida!";
}
?>
