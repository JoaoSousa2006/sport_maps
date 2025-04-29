<?php
if (!empty($_GET['idPlace'])) {
  include_once ('conexao.php');
  $idPlace = $_GET['idPlace'];
  $sqlSelect = "SELECT * from tblplaces WHERE idPlace='$idPlace'";
  $result = $connection->query($sqlSelect);
  // print_r($result);


  if ($result->num_rows > 0) {
    while ($user_data = mysqli_fetch_assoc($result)) {
      $namePlace = $user_data['namePlace'];
      $adressPlace = $user_data['adressPlace'];
      $emailPlace = $user_data['emailPlace'];
      $phonePlace = $user_data['phonePlace'];
      $priceRange = $user_data['priceRange'];
    }

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
        <form action="cad_local.php" method="POST">
          <input value="<?php echo $namePlace ?>" type="text" placeholder="Nome do local" name="namePlace" required />
          <input value="<?php echo $adressPlace ?>" type="text" placeholder="Endereço" name="addressPlace" required />
          <input value="<?php echo $emailPlace ?>" type="email" placeholder="Email de contato" name="emailPlace" required />
          <input value="<?php echo $phonePlace ?>" type="tel" placeholder="Telefone para contato" name="phonePlace" required />
          <select name="priceRange" required>
            <option hidden selected>Faixa de preço</option>
            <option value="free">Gratuito</option>
            <option value="15">Até R$15,00</option>
            <option value="30">Entre R$15,00 e R$30,00</option>
            <option value="50">Entre R$30,00 e R$50,00</option>
            <option value="more">Mais de R$50,00</option>
          </select>
          <button class="submit__btn" type="submit">Cadastrar Local</button>
        </form>
      </div>
    </div>
    <div class="banner-side right-banner"></div>
  </div>
</body>

</html>