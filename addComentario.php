<?php
session_start();
include_once('conexao.php');

if (!isset($_SESSION['idUser'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Usuário não autenticado']);
    exit;
}

$idUser = $_SESSION['idUser'];
$idPlace = intval($_POST['idPlace']);
$comentario = trim($_POST['comentario']);

if ($idPlace && $comentario) {
    $stmt = $connection->prepare("INSERT INTO tblFeedbacks (idUser, idPlace, ContentFeedback) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $idUser, $idPlace, $comentario);
    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['erro' => 'Erro ao salvar comentário']);
    }
    $stmt->close();
} else {
    echo json_encode(['erro' => 'Dados inválidos']);
}
$connection->close();
?>