<?php
include_once('conexao.php');

$idPlace = intval($_GET['idPlace']);
$sql = "SELECT f.ContentFeedback, u.NameUser FROM tblFeedbacks f JOIN tblUsers u ON f.idUser = u.idUser WHERE f.idPlace = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $idPlace);
$stmt->execute();
$result = $stmt->get_result();

$comentarios = [];
while ($row = $result->fetch_assoc()) {
    $comentarios[] = $row;
}
echo json_encode($comentarios);
$stmt->close();
$connection->close();
?>