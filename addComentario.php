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
$rating = intval($_POST['rating']);

if ($idPlace && $comentario && $rating >= 1 && $rating <= 5) {
    $stmt = $connection->prepare("INSERT INTO tblFeedbacks (idUser, idPlace, ContentFeedback, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $idUser, $idPlace, $comentario, $rating);
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