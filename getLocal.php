<?php

header('Content-Type: application/json');
include_once('conexao.php');

$idPlace = isset($_GET['idPlace']) ? intval($_GET['idPlace']) : 0;

$sql = "SELECT * FROM tblplaces WHERE idPlace = $idPlace";
$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    $local = $result->fetch_assoc();
    echo json_encode($local);
} else {
    echo json_encode(['erro' => 'Local não encontrado']);
}
$connection->close();
?>