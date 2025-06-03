<?php
session_start();
// print_r($_SESSION);
// print_r('<br>');
include_once('conexao.php');

if ((!isset($_SESSION['username']) == true) and (!isset($_SESSION['password']) == true)) {
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['nivel_acesso']);
    header(header: 'location:login.html');

    //Teste de funcionamento sessão abaixo
    // print_r('<br>');
    // print_r('não há sessão ativa');
    // print_r('<br>');
} else {
    $logged = $_SESSION['username'];
    $nivel_acesso = $_SESSION['nivel_acesso'];

    // Teste de funcionamento sessão abaixo
    // print_r('<br>');
    // print_r('há sessão ativa: ');
    // print_r($logged);
}

// Verifica se foi feita uma pesquisa
if (empty($_GET['search'])) {
    $sql = "SELECT * FROM tblplaces;";
} else {
    $data = $_GET['search'];
    $sql = "SELECT * FROM tblplaces WHERE NamePlace LIKE '%$data%' OR AdressPlace LIKE '%$data%' OR EmailPlace LIKE '%$data%' OR PhonePlace LIKE '%$data%' OR SportType LIKE '%$data%'";
}

// Consulta SQL com filtros aplicados
$result = $connection->query($sql);

// Verifica erros na consulta
if (!$result) {
    die("Erro na consulta SQL: " . $connection->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="style_lista.css" />
    <!-- <link rel="stylesheet" href="style_cad_local.css" /> -->

    <title>Lista de Locais</title>
</head>

<body>
    <div class="page-container">
        <header class="header">
            <span class="back__btn">
                <a <?php if ($nivel_acesso == 1){echo "href='indexAdmin.html'";} else {echo "href='indexUser.php'";}
                    ?> ><i class="ri-arrow-left-line"></i></a>
            </span>
            <h1>Locais Cadastrados</h1>
            <a href="unLog.php" class="btn sign__in">Sair</a>

        </header>

        <main class="content">
            <input id="search" type="search" placeholder="Buscar locais..." onkeydown="searchPlaces(event)"/>

            <table class="places-table">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Endereço</th>
                        <th scope="col">Email</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Preço</th>
                        <th scope="col">Esporte</th>
                        <?php if ($nivel_acesso == 1)
                            echo "<th>Ações</th>"; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($place_data = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($place_data['NamePlace']) . "</td>";
                        echo "<td>" . htmlspecialchars($place_data['AdressPlace']) . "</td>";
                        echo "<td>" . htmlspecialchars($place_data['EmailPlace']) . "</td>";
                        echo "<td>" . htmlspecialchars($place_data['PhonePlace']) . "</td>";
                        echo "<td>" . htmlspecialchars($place_data['PricePlace']) . "</td>";
                        echo "<td>" . htmlspecialchars($place_data['SportType']) . "</td>";
                        if ($nivel_acesso == 1) {
                            echo "<td class='actions'>";
                            echo "<a href='edit_local.php?idPlace=$place_data[idPlace]'><button class='btn edit'><i class='ri-edit-box-line' ></i></button></a>";
                            echo "<a href='delete.php?idPlace=$place_data[idPlace]'><button class='btn delete'><i class='ri-delete-bin-6-line'></i></button></a>";
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <script>
                function searchPlaces(event) {
                    if (event.key === 'Enter') { // Verifica se a tecla pressionada foi "Enter"
                        const searchInput = document.getElementById('search').value;
                        const url = new URL(window.location.href);
                        url.searchParams.set('search', searchInput); // Atualiza o parâmetro 'search' na URL
                        window.location.href = url; // Recarrega a página com o novo parâmetro
                    }
                }
            </script>
        </main>
    </div>
</body>

</html>