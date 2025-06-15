<?php
if (!empty($_GET['idPlace'])) {
    include_once ('conexao.php');
    $code = $_GET['idPlace'];
    $sqlSelect = "SELECT * from tblplaces WHERE idPlace=$code";
    $result = $connection->query($sqlSelect);


    if ($result->num_rows > 0) {
        $sqlDelete = "DELETE FROM tblplaces WHERE idPlace='$code'";
        $resultDelete = $connection->query($sqlDelete);
        }

    } 
header('location: lista_locais.php')
?>