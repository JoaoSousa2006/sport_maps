<?php
session_start();
include_once("conexao.php");

// Verifica se o usuário está logado e pega o nível de acesso
$nivel_acesso = isset($_SESSION['nivel_acesso']) ? $_SESSION['nivel_acesso'] : 0;

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
    <title>Lista de Locais</title>
</head>
<body>
    <div class="page-container">
        <header class="header">
            <span class="back__btn">
                <a href="index.html"><i class="ri-arrow-left-line"></i></a>
            </span>
            <h1>Locais Cadastrados</h1>
        </header>

        <main class="content">
            <input id="search" type="search" placeholder="Buscar locais..." />

            <table class="places-table">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Endereço</th>
                        <th scope="col">Email</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Preço</th>
                        <?php if ($nivel_acesso == 0) echo "<th>Ações</th>"; ?>
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
                        echo "<td>" . htmlspecialchars($place_data['SportType']) . "</td>";
                        if ($nivel_acesso == 0) {
                            echo "<td class='actions'>";
                            // echo "<a href='edit_local.php?idPlace=$user_data[idPlace]'><button class='btn edit'><i class='ri-edit-box-line' ></i></button></a>";
                            echo "<a href='delete.php?idPlace=$place_data[idPlace]'><button class='btn delete'><i class='ri-delete-bin-6-line'></i></button></a>";
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>