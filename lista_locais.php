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
    <link rel="stylesheet" href="styleLista.css" />
    <link rel="stylesheet" href="styleMap.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />


</head>

<body>
    <div class="page-container">
        <header class="header">
            <span class="back__btn">
                <a <?php if ($nivel_acesso == 1){echo "href='indexAdmin.html'";} else {echo "href='index.html'";} ?> >
                    <i class="ri-arrow-left-line"></i>
                </a>
            </span>
            <a href="unLog.php" class="btn exit">Sair</a>
          </header> 
          
          <main class="content">
          <h1>Locais Cadastrados</h1>
            <input class="search" id="search" type="search" placeholder="Buscar locais ou endereço..." onkeydown="handleSearch(event)"/>
            
            <div class="table-scroll-container">
                <table class="places-table">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <!-- <th scope="col">Endereço</th> -->
                            <th scope="col">Preço</th>
                            <th class="AtvP" scope="col">Atividade Principal</th>
                            <th scope="col">Buscar</th>
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

                
                // row.insertCell().textContent = place.AdressPlace;
                row.insertCell().textContent = place.PricePlace;
                // Célula para a Atividade Principal
        const sportTypeCell = row.insertCell();
        sportTypeCell.textContent = place.SportType;
        sportTypeCell.classList.add('AtvP'); // Adiciona a classe 'atvP-data'

                // Célula para as ações de CRUD (se aplicável)
                let actionsCell;
                actionsCell = row.insertCell();
                actionsCell.innerHTML = `
                    <button class='btn view-map' onclick='zoomToPlace(${parseFloat(place.LatPlace)}, ${parseFloat(place.LongPlace)})'><i class='ri-map-pin-line'></i></button>
                `;
                if (nivelAcesso == 1) {
                    actionsCell = row.insertCell();
                    actionsCell.innerHTML = `
                        <a href='edit_local.php?idPlace=${place.idPlace}'><button class='btn edit'><i class='ri-edit-box-line'></i></button></a>
                        <a href='delete.php?idPlace=${place.idPlace}'><button class='btn delete'><i class='ri-delete-bin-6-line'></i></button></a>
                    `;
                }


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
