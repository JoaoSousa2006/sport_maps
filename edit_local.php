<?php
include_once('conexao.php');

// Verifica se o idPlace foi passado via GET
if (!empty($_GET['idPlace'])) {
    $idPlace = $_GET['idPlace'];

    // Prepara a consulta para buscar os dados do local
    $sqlSelect = "SELECT * FROM tblplaces WHERE idPlace = ?";
    $stmt = $connection->prepare($sqlSelect);
    $stmt->bind_param("i", $idPlace); // 'i' para integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $place_data = $result->fetch_assoc();

        // Extrai os dados do local para variáveis
        $namePlace = $place_data['NamePlace'];
        $adressPlace = $place_data['AdressPlace'];
        $horarioFuncionamento = $place_data['HorarioFuncionamento'];
        $emailPlace = $place_data['EmailPlace'];
        $phonePlace = $place_data['PhonePlace'];
        $pricePlace = $place_data['PricePlace']; // Note: use PricePlace para corresponder ao DB
        $sportType = $place_data['SportType'];
        $latPlace = $place_data['LatPlace'];
        $longPlace = $place_data['LongPlace'];
    } else {
        // Se o idPlace não for encontrado, redireciona ou exibe mensagem de erro
        echo "<center><br>LOCAL NÃO ENCONTRADO.<br></center>";
        echo "<center><br>Redirecionando para a página de listagem em 3 segundos...<br></center>";
        echo '<meta http-equiv="refresh" content="3;url=lista_locais.php">';
        exit();
    }
    $stmt->close();
} else {
    // Se nenhum idPlace foi passado, redireciona
    header('Location: lista_locais.php');
    exit();
}

// Lógica para atualizar o local (será executada quando o formulário for submetido)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idPlace = $_POST['idPlace']; // Pega o ID do campo oculto do formulário
    $newNamePlace = $_POST['namePlace'];
    $newAdressPlace = $_POST['addressPlace']; // Usar 'addressPlace' conforme o HTML
    $newhorarioFuncionamento = $_POST['horarioFuncionamento'];
    $newEmailPlace = $_POST['emailPlace'];
    $newPhonePlace = $_POST['phonePlace'];
    $newPricePlace = $_POST['priceRange']; // Usar 'priceRange' conforme o HTML (agora é text)
    $newSportType = $_POST['sportType'];    // Usar 'sportType' conforme o HTML (agora é text)
    
    // Geocodificação no backend para o novo endereço
    $geocodeResult = geocodeAddress($newAdressPlace);

    if (isset($geocodeResult['error'])) {
        echo "<center><br>ERRO NO ENDEREÇO: " . $geocodeResult['error'] . "<br></center>";
        echo "<center><br>Redirecionando para a página de edição em 5 segundos...<br></center>";
        echo '<meta http-equiv="refresh" content="5;url=edit_local.php?idPlace=' . $idPlace . '">';
        exit;
    }

    $newLatPlace = $geocodeResult['latitude'];
    $newLongPlace = $geocodeResult['longitude'];

    // Atualiza o registro no banco de dados
    $sqlUpdate = "UPDATE tblplaces SET NamePlace = ?, AdressPlace = ?, HorarioFuncionamento = ?, EmailPlace = ?, PhonePlace = ?, PricePlace = ?, SportType = ?, LatPlace = ?, LongPlace = ? WHERE idPlace = ?";
    $stmtUpdate = $connection->prepare($sqlUpdate);

    // 'ssssssdddi' para (NamePlace, AdressPlace, HorarioFuncionamento, EmailPlace, PhonePlace, PricePlace, SportType, LatPlace, LongPlace, idPlace)
    $stmtUpdate->bind_param("ssssssdddi", $newNamePlace, $newAdressPlace, $newhorarioFuncionamento, $newEmailPlace, $newPhonePlace, $newPricePlace, $newSportType, $newLatPlace, $newLongPlace, $idPlace);

    if ($stmtUpdate->execute()) {
        echo "<center><br>LOCAL ATUALIZADO COM SUCESSO!!!<br></center>";
        echo "<center><br>Redirecionando para a página de listagem em 3 segundos...<br></center>";
        echo '<meta http-equiv="refresh" content="3;url=lista_locais.php">';
    } else {
        echo "<center><br>ERRO AO ATUALIZAR LOCAL: " . $stmtUpdate->error . "<br></center>";
    }
    $stmtUpdate->close();
    $connection->close();
    exit; // Termina a execução após a atualização
}

// Função para geocodificar o endereço (repetida de cad_local.php para consistência)
function geocodeAddress($address) {
    $nominatimUrl = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q=" . urlencode($address);
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: SportMapsApp/1.0 (seu_email@example.com)\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($nominatimUrl, false, $context);

    if ($response === FALSE) {
        return ['error' => 'Erro ao conectar ao serviço de geocodificação.'];
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Resposta inválida do serviço de geocodificação.'];
    }

    if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
        return [
            'latitude' => (float)$data[0]['lat'],
            'longitude' => (float)$data[0]['lon']
        ];
    } else {
        return ['error' => 'Endereço não encontrado ou inválido. Tente ser mais específico.'];
    }
}

// Seu HTML começa aqui, replicando a estrutura de cad_local.html
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet" />
  <link rel="stylesheet" href="root.css" />
  <link rel="stylesheet" href="styleCad.css" />
  <title>Editar Local</title>
</head>

<body>
  <div class="container">
    <div class="banner-side left-banner"></div>
    <div class="content">
      <div class="header">
        <span class="back__btn">
          <a href="lista_locais.php"><i class="ri-arrow-left-line"></i></a>
        </span>
      </div>
      <div class="form__content">
        <h3 class="form__title">Editar Local</h3>
        <p class="form__subtitle">
          Atualize as informações do seu local favorito!
        </p>
        <form action="edit_local.php" method="POST">
          <input type="hidden" name="idPlace" value="<?php echo htmlspecialchars($idPlace); ?>">

          <input type="text" placeholder="Nome do local" name="namePlace" value="<?php echo htmlspecialchars($namePlace); ?>" required />
          <input type="text" placeholder="Endereço" name="addressPlace" value="<?php echo htmlspecialchars($adressPlace); ?>" required />
          <input type="text" name="horarioFuncionamento" placeholder="Horário de Funcionamento" value="<?php echo htmlspecialchars($horarioFuncionamento); ?>" required />
          <input type="email" placeholder="Email de contato" name="emailPlace" value="<?php echo htmlspecialchars($emailPlace); ?>" required />
          <input type="tel" placeholder="Telefone para contato" name="phonePlace" value="<?php echo htmlspecialchars($phonePlace); ?>" required />
          <input type="number" placeholder="Faixa de preço" name="priceRange" value="<?php echo htmlspecialchars($pricePlace); ?>" required>
          <input type="text" placeholder="Esporte praticado" name="sportType" value="<?php echo htmlspecialchars($sportType); ?>" required>
          
          <button type="submit" class="register m_in">Salvar Alterações</button>
        </form>
        <style>
          .m_in {
            margin-inline: 10vw;
          }
          @media (width < 768px) {
            .m_in {
              margin-inline: 20vw;
            }
            }
            @media (width > 1024px) {
            .m_in {
              margin-inline: 10vw;
            }
          }

        </style>
      </div>
    </div>
    <div class="banner-side right-banner"></div>
  </div>
</body>

</html>