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
$vote = intval($_POST['vote']); // +1 para upvote, -1 para downvote

// Verifica se já existe voto desse usuário para esse local
$sql = "SELECT idScore, votes FROM placesScores WHERE idUser = ? AND idPlace = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $idUser, $idPlace);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Atualiza voto
    $newVotes = $vote;
    $update = $connection->prepare("UPDATE placesScores SET votes = ? WHERE idScore = ?");
    $update->bind_param("ii", $newVotes, $row['idScore']);
    $update->execute();
    $update->close();
} else {
    // Insere novo voto
    $insert = $connection->prepare("INSERT INTO placesScores (idUser, idPlace, votes) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $idUser, $idPlace, $vote);
    $insert->execute();
    $insert->close();
}
$stmt->close();

// Retorna total de votos para o local
$total = $connection->query("SELECT COALESCE(SUM(votes),0) as total FROM placesScores WHERE idPlace = $idPlace")->fetch_assoc()['total'];
echo json_encode(['total' => intval($total)]);
$connection->close();
?>