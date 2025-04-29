<?php
session_start();
include_once('conexao.php');

// Protege acesso apenas para Admin
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] != 1) {
  header('Location: login.php');
  exit();
}

if (!empty($_GET['idPlace'])) {
  $idPlace = $_GET['idPlace'];
  $sqlSelect = "SELECT * FROM tblPlaces WHERE idPlace='$idPlace'";
  $result = $connection->query($sqlSelect);

  if ($result->num_rows > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $namePlace = $user_data['NamePlace'];
    $adressPlace = $user_data['AdressPlace'];
    $emailPlace = $user_data['EmailPlace'];
    $phonePlace = $user_data['PhonePlace'];
    $pricePlace = $user_data['PricePlace'];
  } else {
    header('Location: lista_locais.php');
    exit();
  }
} else {
  header('Location: lista_locais.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="style_cad_local.css">
  <title>Editar Local</title>
</head>
<body>
  <div class="container center-layout">
    <div class="banner-side left-banner"></div>
    <div class="content">
      <div class="header">
        <span class="back__btn">
          <a href="lista_locais.php"><i class="ri-arrow-left-line"></i> Voltar</a>
        </span>
      </div>
      <div class="form__content">
        <h3 class="form__title">Editar Local</h3>
        <p class="form__subtitle">Atualize as informações do local:</p>
        <form action="salvar_edicao_local.php" method="POST">
          <input type="hidden" name="idPlace" value="<?php echo $idPlace; ?>">
          <input value="<?php echo $namePlace; ?>" type="text" placeholder="Nome do local" name="namePlace" required>
          <input value="<?php echo $adressPlace; ?>" type="text" placeholder="Endereço" name="adressPlace" required>
          <input value="<?php echo $emailPlace; ?>" type="email" placeholder="Email de contato" name="emailPlace">
          <input value="<?php echo $phonePlace; ?>" type="tel" placeholder="Telefone para contato" name="phonePlace">
          <input value="<?php echo $pricePlace; ?>" type="number" placeholder="Preço médio" name="pricePlace" step="0.01">
          <button class="submit__btn" type="submit">Salvar Alterações</button>
        </form>
      </div>
    </div>
    <div class="banner-side right-banner"></div>
  </div>
</body>
</html>
