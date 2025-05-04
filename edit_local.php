<?php
if (!empty($_GET['idPlace'])) {
  include_once ('conexao.php');
  $idPlace = $_GET['idPlace'];
  $sqlSelect = "SELECT * from tblplaces WHERE idPlace = '$idPlace'";
  // print_r($sqlSelect);
  $result = $connection->query($sqlSelect);
  // print_r($result);
  
  
  
  
  if ($result->num_rows > 0) {
    while ($place_data = mysqli_fetch_assoc($result)) {
      // print_r($place_data);
      $idPlace = $place_data["idPlace"];
      // echo "idPlace: $idPlace<br>";

      $namePlace = $place_data['NamePlace'];
      // echo "namePlace: $namePlace<br>";
      
      $AdressPlace = $place_data['AdressPlace'];
      // echo "AdressPlace: $AdressPlace<br>";
      
      $emailPlace = $place_data['EmailPlace'];
      // echo "emailPlace: $emailPlace<br>";
      
      $phonePlace = $place_data['PhonePlace'];
      // echo "phonePlace: $phonePlace<br>";
      
      $PricePlace = $place_data['PricePlace'];
      // echo "PricePlace: $PricePlace<br>";

      $sportType = $place_data['SportType']
    }
    print_r($idPlace);
  } else {
    header('location:lista_locais.php');
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet" />
  <link rel="stylesheet" href="style_cad_local.css" />
  <title>Cadastro de Locais</title>
</head>

<body>
  <div class="container center-layout">
    <div class="banner-side left-banner"></div>
    <div class="content">
      <div class="header">
        <span class="back__btn">
          <a href="index.html"><i class="ri-arrow-left-line"></i></a>
        </span>
      </div>
      <div class="form__content">
        <h3 class="form__title">Cadastro de Locais</h3>
        <p class="form__subtitle">
          Compartilhe conosco seus lugares favoritos para prática esportiva!
        </p>
        <form action="saveEdit.php" method="POST">
          <input type="hidden" name="idPlace" value="<?php echo $idPlace ?>" />
          <input value="<?php echo $namePlace ?>" type="text" placeholder="Nome do local" name="namePlace" required />
          <input value="<?php echo $AdressPlace ?>" type="text" placeholder="Endereço" name="adressPlace" required />
          <input value="<?php echo $emailPlace ?>" type="email" placeholder="Email de contato" name="emailPlace" required />
          <input value="<?php echo $phonePlace ?>" type="tel" placeholder="Telefone para contato" name="phonePlace" required />
          <select name="pricePlace" required>
            <option hidden>Faixa de preço</option>
            <option value="gratuito">Gratuito</option>
            <option value="até R$15">Até R$15,00</option>
            <option value="de R$15 a R$30">Entre R$15,00 e R$30,00</option>
            <option value="de R$30 a R$50">Entre R$30,00 e R$50,00</option>
            <option value="+R$50">Mais de R$50,00</option>
          </select>
          <select name="sportType" required>
            <option hidden>Esporte</option>
            <option value="Skate">Skate</option>
            <option value="Bicicleta">Bicicleta</option>
            <option value="Corrida">Corrida</option>
            <option value="Patins">Patins</option>
            <option value="Quadra">Quadra</option>
          </select>
          <button class="submit__btn" type="submit">Salvar alterações</button>
        </form>
      </div>
    </div>
    <div class="banner-side right-banner"></div>
  </div>
</body>

</html>
