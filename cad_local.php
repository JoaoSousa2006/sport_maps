<?php
include_once('conexao.php');
$namePlace = $_POST['namePlace'];
echo "namePlace: $namePlace<br>";

$adressPlace = $_POST['addressPlace'];
echo "adressPlace: $adressPlace<br>";

$emailPlace = $_POST['emailPlace'];
echo "emailPlace: $emailPlace<br>";

$phonePlace = $_POST['phonePlace'];
echo "phonePlace: $phonePlace<br>";

$priceRange = $_POST['priceRange'];
echo "priceRange: $priceRange<br>";

$sportType = $_POST['sportType'];
echo "sportType: $sportType<br>";

$result = mysqli_query($connection, "SELECT * FROM tblplaces WHERE adressPlace LIKE '$adressPlace'");

print_r("<BR>");
print_r($result);

if (mysqli_num_rows($result) > 0) {
    header("Location: cad_local.html");
    echo "<center><br>LOCAL JÁ CADASTRADO!!!<br></center>";
    exit; // Interrompe a execução do restante do script
} else {
    // print_r($result);
    // echo "<center><br>USUÁRIO CADASTRADO!!!<br></center>";
    $result = mysqli_query($connection, query: "INSERT INTO `tblplaces` (`NamePlace`, `AdressPlace`, `EmailPlace`, `PhonePlace`, `PricePlace`, `SportType`) 
    VALUES ('$namePlace', '$adressPlace', '$emailPlace', '$phonePlace', '$priceRange','$sportType')");
    print_r($result);
    header('Location: lista_locais.php');
    exit; // Interrompe a execução do restante do script
}

?>
