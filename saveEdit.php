<?php
include_once ('conexao.php');

    $id = $_POST['idPlace'];
    $idPlace = $_POST["idPlace"];
    echo "idPlace: $idPlace<br>";

    $namePlace = $_POST['namePlace'];
    echo "namePlace: $namePlace<br>";
    
    $AdressPlace = $_POST['adressPlace'];
    echo "AdressPlace: $AdressPlace<br>";
    
    $emailPlace = $_POST['emailPlace'];
    echo "emailPlace: $emailPlace<br>";
    
    $phonePlace = $_POST['phonePlace'];
    echo "phonePlace: $phonePlace<br>";
    
    $PricePlace = $_POST['pricePlace'];
    echo "PricePlace: $PricePlace<br>";

    $sqlUpdate = "UPDATE `tblplaces` SET `NamePlace` = '$namePlace', `AdressPlace` = '$AdressPlace', `EmailPlace` = '$emailPlace', `PhonePlace` = '$phonePlace', `PricePlace` = '$PricePlace' WHERE `tblplaces`.`idPlace` = '$idPlace'";


    $result = $connection->query($sqlUpdate);

header('location:lista_locais.php');