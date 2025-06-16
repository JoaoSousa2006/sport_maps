<?php
include_once('conexao.php');

// Função para geocodificar o endereço usando Nominatim
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

// Inicializa a mensagem e o tipo de mensagem
$message = "";
$messageType = ""; // 'success' ou 'error'
$redirectUrl = "";
$redirectDelay = 3; // Redireciona após 3 segundos

// Obter dados do formulário
$namePlace = $_POST['namePlace'] ?? '';
$adressPlace = $_POST['addressPlace'] ?? '';
$horarioFuncionamento = $_POST['horarioFuncionamento'] ?? '';
$emailPlace = $_POST['emailPlace'] ?? '';
$phonePlace = $_POST['phonePlace'] ?? '';
$priceRange = $_POST['priceRange'] ?? '';
$sportType = $_POST['sportType'] ?? '';

// 1. Validar e Geocodificar o endereço
$geocodeResult = geocodeAddress($adressPlace);

if (isset($geocodeResult['error'])) {
    $message = "ERRO NO ENDEREÇO: " . $geocodeResult['error'];
    $messageType = "error";
    $redirectUrl = "cad_local.html";
    $redirectDelay = 5; // Mais tempo para o usuário ler o erro de endereço
} else {
    // Se o endereço for válido, obtenha as coordenadas
    $latPlace = $geocodeResult['latitude'];
    $longPlace = $geocodeResult['longitude'];

    // 2. Verificar se o local já existe pelo endereço
    $stmtCheck = $connection->prepare("SELECT COUNT(*) FROM tblplaces WHERE AdressPlace = ?");
    $stmtCheck->bind_param("s", $adressPlace);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count > 0) {
        $message = "LOCAL JÁ CADASTRADO!!! O endereço '" . htmlspecialchars($adressPlace) . "' já existe em nossos registros.";
        $messageType = "error";
        $redirectUrl = "cad_local.html";
    } else {
        // 3. Inserir dados no banco de dados, incluindo Latitude e Longitude
        $sql = "INSERT INTO `tblplaces` (`NamePlace`, `AdressPlace`, `EmailPlace`, `PhonePlace`, `PricePlace`, `SportType`, `LatPlace`, `LongPlace`, `HorarioFuncionamento`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);

        if ($stmt === false) {
            $message = "Erro interno ao preparar a inserção: " . $connection->error;
            $messageType = "error";
            $redirectUrl = "cad_local.html";
        } else {
            // "ssssssdds" representa os tipos dos parâmetros:
            // s: string (NamePlace, AdressPlace, EmailPlace, PhonePlace, PricePlace, SportType, HorarioFuncionamento)
            // d: double (LatPlace, LongPlace)
            $stmt->bind_param("ssssssdds", $namePlace, $adressPlace, $emailPlace, $phonePlace, $priceRange, $sportType, $latPlace, $longPlace, $horarioFuncionamento);

            if ($stmt->execute()) {
                $message = "LOCAL CADASTRADO COM SUCESSO!!!";
                $messageType = "success";
                $redirectUrl = "lista_locais.php";
            } else {
                $message = "ERRO AO CADASTRAR LOCAL: " . $stmt->error;
                $messageType = "error";
                $redirectUrl = "cad_local.html";
            }
            $stmt->close();
        }
    }
}
$connection->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="refresh" content="<?php echo $redirectDelay; ?>;url=<?php echo $redirectUrl; ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="root.css" />
    <link rel="stylesheet" href="styleCad.css" />
    <title>Processando Cadastro</title>
    <style>
        /* Estilo para a mensagem centralizada */
        .message-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            flex-direction: column;
        }
        .message-box {
            background-color: var(--yellow-glow);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 90%;
        }
        .message-box h3 {
            color: var(--text-dark);
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        .message-box p {
            color: var(--text-light);
            font-size: 1.1rem;
        }
        .message-box.error h3 {
            color: var(--corrupted-oil); /* Uma cor de erro mais forte do seu palette */
        }
        .message-box.success h3 {
            color: var(--primary-color-dark); /* Cor de sucesso do seu palette */
        }
        .message-box a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: var(--primary-color-dark);
            color: var(--extra-light);
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .message-box a:hover {
            background-color: var(--uzi-hoodie);
        }
    </style>
</head>
<body>
    <div class="container message-container">
        <div class="message-box <?php echo $messageType; ?>">
            <h3 class="form__title"><?php echo $message; ?></h3>
            <p>Você será redirecionado em <?php echo $redirectDelay; ?> segundos...</p>
            <a href="<?php echo $redirectUrl; ?>">Ou clique aqui para ir agora</a>
        </div>
    </div>
</body>
</html>