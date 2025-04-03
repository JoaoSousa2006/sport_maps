<?php
$servidor = "127.0.0.1"; 
$usuario = "root"; 
$senha = "usbw";
$banco = "sport_maps";

// Conectar ao banco de dados
$conn = mysqli_connect($servidor, $usuario, $senha, $banco);

// Verificar a conexão
if (!$conn) {
    die("Deu não :T " . mysqli_connect_error());
}
// Se conectar com sucesso
else{
    echo "Conexao bem-sucedida!";}
?>
