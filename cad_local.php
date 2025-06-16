<?php
include_once('conexao.php');

// Função para geocodificar o endereço usando Nominatim
function geocodeAddress($address) {
    // URL da API do Nominatim
    $nominatimUrl = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q=" . urlencode($address);

    // Adicione um User-Agent para evitar ser bloqueado pela API
    // É uma boa prática informar a origem da sua requisição
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: SportMapsApp/1.0 (seu_email@example.com)\r\n"
        ]
    ];
    $context = stream_context_create($options);

    // Faz a requisição HTTP
    // Você pode usar cURL para mais robustez, mas file_get_contents é mais simples para este caso
    $response = @file_get_contents($nominatimUrl, false, $context);

    if ($response === FALSE) {
        // Erro na requisição (rede, servidor Nominatim fora do ar, etc.)
        return ['error' => 'Erro ao conectar ao serviço de geocodificação.'];
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        // Erro ao decodificar JSON
        return ['error' => 'Resposta inválida do serviço de geocodificação.'];
    }

    if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
        // Endereço encontrado
        return [
            'latitude' => (float)$data[0]['lat'],
            'longitude' => (float)$data[0]['lon']
        ];
    } else {
        // Endereço não encontrado ou dados incompletos
        return ['error' => 'Endereço não encontrado ou inválido. Tente ser mais específico.'];
    }
}

// Obter dados do formulário
$namePlace = $_POST['namePlace'];
$adressPlace = $_POST['addressPlace'];
$emailPlace = $_POST['emailPlace'];
$phonePlace = $_POST['phonePlace'];
$priceRange = $_POST['priceRange'];
$sportType = $_POST['sportType'];

// 1. Validar e Geocodificar o endereço
$geocodeResult = geocodeAddress($adressPlace);

if (isset($geocodeResult['error'])) {
    // Endereço inválido ou erro na geocodificação
    echo "<center><br>ERRO NO ENDEREÇO: " . $geocodeResult['error'] . "<br></center>";
    echo "<center><br>Redirecionando para a página de cadastro em 5 segundos...<br></center>";
    // Redireciona de volta para o formulário após um tempo, para o usuário ver a mensagem
    echo '<meta http-equiv="refresh" content="5;url=cad_local.html">';
    exit;
}

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
    echo "<center><br>LOCAL JÁ CADASTRADO!!!<br></center>";
    echo "<center><br>Redirecionando para a página de cadastro em 3 segundos...<br></center>";
    echo '<meta http-equiv="refresh" content="3;url=cad_local.html">';
    exit;
} else {
    // 3. Inserir dados no banco de dados, incluindo Latitude e Longitude
    // Certifique-se de que sua tabela tblplaces tenha as colunas LatPlace DECIMAL(10, 7) e LongPlace DECIMAL(10, 7)
    $sql = "INSERT INTO `tblplaces` (`NamePlace`, `AdressPlace`, `EmailPlace`, `PhonePlace`, `PricePlace`, `SportType`, `LatPlace`, `LongPlace`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);

    if ($stmt === false) {
        die("Erro na preparação da consulta: " . $connection->error . " SQL: " . $sql);
    }

    // "ssssssdd" representa os tipos dos parâmetros para bind_param:
    // s: string (NamePlace)
    // s: string (AdressPlace)
    // s: string (EmailPlace)
    // s: string (PhonePlace)
    // s: string (PricePlace)
    // s: string (SportType)
    // d: double (LatPlace) - DECIMAL pode ser tratado como double ou float no PHP
    // d: double (LongPlace) - DECIMAL pode ser tratado como double ou float no PHP
    $stmt->bind_param("ssssssdd", $namePlace, $adressPlace, $emailPlace, $phonePlace, $priceRange, $sportType, $latPlace, $longPlace);

    if ($stmt->execute()) {
        echo "<center><br>LOCAL CADASTRADO COM SUCESSO!!!<br></center>";
        echo "<center><br>Redirecionando para a página de listagem de locais em 3 segundos...<br></center>";
        echo '<meta http-equiv="refresh" content="3;url=lista_locais.php">';
    } else {
        echo "<center><br>ERRO AO CADASTRAR LOCAL: " . $stmt->error . "<br></center>";
    }

    $stmt->close();
    $connection->close();
}
?>