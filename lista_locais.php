<?php
session_start();
include_once('conexao.php');

if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['nivel_acesso']);
    header('location:login.html');
    exit(); // Importante para parar a execução do script
} else {
    $logged = $_SESSION['username'];
    $nivel_acesso = $_SESSION['nivel_acesso'];
}

// O PHP agora SEMPRE busca todos os locais
// Certifique-se de selecionar LatPlace e LongPlace explicitamente
$sql = "SELECT idPlace, NamePlace, AdressPlace, PricePlace, SportType, LatPlace, LongPlace FROM tblplaces;";

// Consulta SQL com filtros aplicados
$result = $connection->query($sql);

// Verifica erros na consulta
if (!$result) {
    die("Erro na consulta SQL: " . $connection->error);
}

// Array para armazenar todos os dados dos locais (tanto para a tabela quanto para o mapa)
$all_places_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_places_data[] = $row; // Armazena todos os dados de cada linha
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Lista de Locais</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="style_lista.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }
        .page-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }

        .back__btn i {
            margin-right: 5px;
        }
        .content {
            padding: 20px 0;
        }
        #search {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .actions {
            white-space: nowrap;
        }
        .btn.edit, .btn.delete, .btn.view-map { /* Adicionado estilo para o novo botão */
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            font-size: 1em; /* Ajuste o tamanho da fonte */
        }
        .btn.edit { background-color: #007bff; }
        .btn.delete { background-color: #dc3545; }
        .btn.view-map { background-color: #28a745; } /* Cor para o novo botão */


        /* Estilo para o container do mapa */
        #mapid {
            height: 400px; /* Altura do mapa */
            width: 100%;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* --- NOVOS ESTILOS PARA ROLAGEM DA TABELA (INÍCIO) --- */
        .table-scroll-container {
            max-height: 400px; /* Defina a altura máxima desejada para a área de rolagem */
            overflow-y: auto; /* Habilita a rolagem vertical quando o conteúdo excede a altura máxima */
            overflow-x: visible; /* Evita rolagem horizontal */
            border: 1px solid #ddd; /* Opcional: Adiciona uma borda para delimitar a área de rolagem */
            margin-bottom: 20px; /* Espaço entre a tabela e o mapa, se houver */
        }

        .places-table { /* Usa a classe da sua tabela existente */
            width: 100%; /* Faz a tabela ocupar 100% da largura do contêiner de rolagem */
            border-collapse: collapse; /* Para que as bordas da tabela e células se combinem */
            margin-bottom: 0; /* Remove margem inferior que possa causar barra de rolagem desnecessária */
        }

        .places-table th, .places-table td {
            padding: 10px; /* Ajustado para um padding padrão, mas use o que você já tem em 'style_lista.css' se for diferente */
            border: 1px solid #ddd; /* Bordas das células */
            text-align: left;
            white-space: nowrap; /* Evita que o texto quebre linhas, mantendo as colunas compactas */
        }

        .places-table thead {
            position: sticky; /* Fixa o cabeçalho ao rolar */
            top: 0; /* Fixa no topo do contêiner de rolagem */
            background-color: #f8f9fa; /* Cor de fundo do cabeçalho fixo (pode ajustar) */
            z-index: 10; /* Garante que o cabeçalho fique acima do conteúdo ao rolar */
        }
        /* --- NOVOS ESTILOS PARA ROLAGEM DA TABELA (FIM) --- */
    </style>
</head>

<body>
    <div class="page-container">
        <header class="header">
            <span class="back__btn">
                <a <?php if ($nivel_acesso == 1){echo "href='indexAdmin.html'";} else {echo "href='indexUser.php'";} ?> >
                    <i class="ri-arrow-left-line"></i>
                </a>
            </span>
            <h1>Locais Cadastrados</h1>
            <a href="unLog.php" class="btn sign__in">Sair</a>
        </header> 

        <main class="content">
            <input id="search" type="search" placeholder="Buscar locais ou endereço..." onkeydown="handleSearch(event)"/>
            
            <div class="table-scroll-container">
                <table class="places-table">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Endereço</th>
                            <th scope="col">Preço</th>
                            <th scope="col">Atividade Principal</th>
                            <?php if ($nivel_acesso == 1)
                                echo "<th>Ações</th>"; ?>
                        </tr>
                    </thead>
                    <tbody id="placesTableBody">
                        </tbody>
                </table>
            </div> <div id="mapid"></div>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Dados dos locais passados do PHP para o JavaScript
        const allPlacesData = <?php echo json_encode($all_places_data); ?>;
        const nivelAcesso = <?php echo json_encode($nivel_acesso); ?>; // Passa o nível de acesso para JS

        let currentMarkers = L.featureGroup(); // Grupo para gerenciar os marcadores dos locais
        let searchResultMarker = null; // Marcador para o resultado da busca de endereço

        // --- Configuração e Inicialização do Mapa Leaflet ---
        let mapCenter = [-23.6698, -46.4617]; // Padrão: Mauá, SP
        let mapZoom = 12; // Zoom padrão (um pouco mais amplo)

        const mymap = L.map('mapid').setView(mapCenter, mapZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mymap);

        // Adiciona o grupo de marcadores ao mapa
        currentMarkers.addTo(mymap);

        /**
         * Preenche a tabela com os dados fornecidos.
         * @param {Array} dataToDisplay - Array de objetos de locais a serem exibidos.
         */
        function populateTable(dataToDisplay) {
            const tableBody = document.getElementById('placesTableBody');
            tableBody.innerHTML = ''; // Limpa o corpo da tabela

            if (dataToDisplay.length === 0) {
                // Colspan ajustado para o número total de colunas visíveis
                const totalColumns = nivelAcesso == 1 ? 8 : 7; // 7 colunas fixas + 1 de Ações se for admin
                tableBody.innerHTML = `<tr><td colspan="${totalColumns}" style="text-align: center;">Nenhum local encontrado.</td></tr>`;
                return;
            }

            dataToDisplay.forEach(place => {
                const row = tableBody.insertRow();

                // Torna o nome do local um link para a página de detalhes
                const nameCell = row.insertCell();
                nameCell.innerHTML = `<a href="LocalDetalhes.html?idPlace=${place.idPlace}">${place.NamePlace}</a>`;

                row.insertCell().textContent = place.NamePlace;
                row.insertCell().textContent = place.AdressPlace;
                row.insertCell().textContent = place.PricePlace;
                row.insertCell().textContent = place.SportType;

                // Célula para as ações de CRUD (se aplicável)
                let actionsCell;
                if (nivelAcesso == 1) {
                    actionsCell = row.insertCell();
                    actionsCell.className = 'actions';
                    actionsCell.innerHTML = `
                        <a href='edit_local.php?idPlace=${place.idPlace}'><button class='btn edit'><i class='ri-edit-box-line'></i></button></a>
                        <a href='delete.php?idPlace=${place.idPlace}'><button class='btn delete'><i class='ri-delete-bin-6-line'></i></button></a>
                        <button class='btn view-map' onclick='zoomToPlace(${parseFloat(place.LatPlace)}, ${parseFloat(place.LongPlace)})'>
                            <i class='ri-map-pin-line'></i>
                        </button>
                    `;
                }

                // Célula para o botão "Ver no Mapa"
                const viewMapCell = row.insertCell();

            });
        }

        /**
         * Atualiza os marcadores no mapa com base nos dados fornecidos.
         * @param {Array} dataToDisplay - Array de objetos de locais a serem marcados.
         */
        function updateMapMarkers(dataToDisplay) {
            currentMarkers.clearLayers(); // Remove todos os marcadores existentes do grupo

            let bounds = L.latLngBounds([]); // Para ajustar o mapa aos marcadores

            dataToDisplay.forEach(place => {
                if (place.LatPlace && place.LongPlace) {
                    const lat = parseFloat(place.LatPlace);
                    const lon = parseFloat(place.LongPlace);
                    const marker = L.marker([lat, lon]);
                    marker.bindPopup(`<b>${place.NamePlace}</b><br>${place.AdressPlace}<br>Esporte: ${place.SportType}`);
                    currentMarkers.addLayer(marker); // Adiciona o marcador ao grupo
                    bounds.extend([lat, lon]); // Estende o limite para incluir o marcador
                }
            });

            if (bounds.isValid()) {
                mymap.fitBounds(bounds, { padding: [50, 50] }); // Ajusta o mapa para mostrar todos os marcadores
            } else {
                // Se não houver marcadores ou coordenadas válidas, retorna ao centro padrão
                mymap.setView(mapCenter, mapZoom);
            }
        }

        /**
         * Lida com a busca no campo de input.
         * Filtra a tabela e atualiza o mapa.
         * @param {Event} event - O evento de teclado.
         */
        async function handleSearch(event) {
            if (event.key === 'Enter') {
                const searchTerm = document.getElementById('search').value.toLowerCase().trim();

                // Remove o marcador de busca anterior, se houver
                if (searchResultMarker) {
                    mymap.removeLayer(searchResultMarker);
                    searchResultMarker = null;
                }

                let filteredPlaces = [];
                let geocodedLocation = null;

                if (searchTerm === '') {
                    // Se o campo estiver vazio, exibe todos os locais
                    filteredPlaces = allPlacesData;
                } else {
                    // Tenta filtrar por nome, endereço, email, telefone, esporte
                    filteredPlaces = allPlacesData.filter(place =>
                        (place.NamePlace && place.NamePlace.toLowerCase().includes(searchTerm)) ||
                        (place.AdressPlace && place.AdressPlace.toLowerCase().includes(searchTerm)) ||
                        (place.SportType && place.SportType.toLowerCase().includes(searchTerm))
                    );

                    // Se não encontrou nenhum local pelos critérios acima, tenta geocodificar o termo como endereço
                    if (filteredPlaces.length === 0) {
                        try {
                            // Adiciona um contexto à busca para melhorar a precisão, por exemplo, a cidade/estado
                            const nominatimUrl = `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(searchTerm + ", Mauá, SP, Brasil")}`;
                            const response = await fetch(nominatimUrl);
                            const data = await response.json();

                            if (data && data.length > 0) {
                                geocodedLocation = {
                                    lat: parseFloat(data[0].lat),
                                    lon: parseFloat(data[0].lon),
                                    display_name: data[0].display_name
                                };
                            } else {
                                // console.log("Endereço não encontrado pelo serviço de geocodificação para: " + searchTerm);
                            }
                        } catch (error) {
                            console.error("Erro na geocodificação:", error);
                        }
                    }
                }

                // Atualiza a tabela
                populateTable(filteredPlaces);

                // Atualiza os marcadores dos locais no mapa
                updateMapMarkers(filteredPlaces);

                // Se houver uma localização geocodificada (de uma busca por endereço que não é um local cadastrado)
                if (geocodedLocation) {
                    searchResultMarker = L.marker([geocodedLocation.lat, geocodedLocation.lon], {
                        icon: L.markerClusterGroup ? L.AwesomeMarkers.icon({ icon: 'home', markerColor: 'blue' }) : L.icon({ iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png' })
                    }).addTo(mymap);
                    searchResultMarker.bindPopup(`<b>${geocodedLocation.display_name}</b><br>Resultado da Busca`).openPopup();
                    mymap.setView([geocodedLocation.lat, geocodedLocation.lon], 15); // Centraliza e dá zoom na busca
                } else if (filteredPlaces.length === 0 && searchTerm !== '') {
                    // Se não encontrou locais e não geocodificou, centraliza no padrão ou informa
                    mymap.setView(mapCenter, mapZoom);
                    // alert("Nenhum local ou endereço encontrado para '" + searchTerm + "'.");
                }
            }
        }

        /**
         * Centraliza o mapa em uma localização específica e abre o popup do marcador.
         * @param {number} lat - Latitude.
         * @param {number} lon - Longitude.
         */
        function zoomToPlace(lat, lon) {
            mymap.setView([lat, lon], 17); // Centraliza o mapa com um zoom mais próximo (17 é bom para ruas)

            // Opcional: Tentar abrir o popup do marcador correspondente
            currentMarkers.eachLayer(function(marker) {
                if (marker.getLatLng().lat === lat && marker.getLatLng().lng === lon) {
                    marker.openPopup();
                }
            });
        }

        // Inicializa a tabela e o mapa com todos os dados quando a página carrega
        document.addEventListener('DOMContentLoaded', () => {
            populateTable(allPlacesData);
            updateMapMarkers(allPlacesData);
        });
    </script>
    <script>
        // Esta função searchPlaces foi renomeada para handleSearch e já está incorporada acima.
        // Se você não a estiver chamando em mais nenhum lugar, esta função pode ser removida.
        // function searchPlaces(event) {
        //     if (event.key === 'Enter') {
        //         const searchInput = document.getElementById('search').value;
        //         const url = new URL(window.location.href);
        //         url.searchParams.set('search', searchInput);
        //         window.location.href = url;
        //     }
        // }
    </script>

</body>
</html>
