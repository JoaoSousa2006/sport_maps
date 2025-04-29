<?php
include_once('conexao.php');
$namePlace = isset($_POST['namePlace']) ? $_POST['namePlace'] : '';
$adressPlace = isset($_POST['adressPlace']) ? $_POST['adressPlace'] : '';
$emailPlace = isset($_POST['emailPlace']) ? $_POST['emailPlace'] : '';
$phonePlace = isset($_POST['phonePlace']) ? $_POST['phonePlace'] : '';
$priceRange = isset($_POST['priceRange']) ? $_POST['priceRange'] : '';

$result = mysqli_query($connection, "SELECT * FROM tblplaces WHERE adressPlace LIKE '$adressPlace'");

print_r("<BR>");
print_r($result);

if (mysqli_num_rows($result) > 0) {
    // header("Location: cad_local. html");
    exit; // Interrompe a execução do restante do script
    // echo "<center><br>USUÁRIO JÁ CADASTRADO!!!<br></center>";
} else {
    // print_r($result);
    // echo "<center><br>USUÁRIO CADASTRADO!!!<br></center>";
    $result = mysqli_query($connection, query: "INSERT INTO `tblplaces` 
    (`idPlace`, `NamePlace`, `AdressPlace`, `EmailPlace`, `PhonePlace`, `LatPlace`, `LongPlace`, `PricePlace`, `SportType`) 
    VALUES (NULL, '$namePlace', '$adressPlace', '$emailPlace', '$phonePlace', '$priceRange', '$', '$', '$')");
    print_r($result);
    header('Location: lista_locais.php');
    exit; // Interrompe a execução do restante do script
}

?>