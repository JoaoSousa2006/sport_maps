<?php
include_once('conexao.php');
$idPlace = intval($_GET['idPlace']);
$sql = "SELECT AVG(rating) as average FROM tblFeedbacks WHERE idPlace = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $idPlace);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
echo json_encode(['media' => floatval($row['average']), 'average' => floatval($row['average'])]);
$stmt->close();
$connection->close();
?>