<?php
session_start();
include_once("conexao.php");

// Verifica se o usuário está logado e pega o nível de acesso
$nivel_acesso = isset($_SESSION['nivel_acesso']) ? $_SESSION['nivel_acesso'] : 0;

// Verifica se foi feita uma pesquisa
$where = "1=1"; // Condição padrão

if (!empty($_GET['search'])) {
    $data = $_GET['search'];
    $where .= " AND (nome LIKE '%$data%' OR endereco LIKE '%$data%' OR faixa_preco LIKE '%$data%' OR esportes LIKE '%$data%')";
}

// Consulta SQL com filtros aplicados
$sql = "SELECT * FROM locais WHERE $where ORDER BY id DESC;";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>Lista de Locais</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; padding: 20px; background: white; box-shadow: 0px 0px 10px gray; border-radius: 8px; }
        input, select { padding: 10px; margin: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        a { color: #fff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="w3-container w3-padding w3-light-grey w3-right-align">
        <span>Bem-vindo, <?php echo $_SESSION["user_email"]; ?>!</span>
        <a href="logout.php" class="w3-button w3-red">Sair</a>
    </div>
    <div class="container">
        <h1>Lista de Locais</h1>
        
        <form method="GET">
            <input type="text" name="search" placeholder="Buscar por nome, endereço, faixa de preço ou esportes...">
            <button type="submit">Buscar</button>
        </form>

        <table>
            <tr>
                <th>Local</th>
                <th>Endereço</th>
                <th>Faixa de Preço</th>
                <th>Esportes</th>
                <?php if ($nivel_acesso == 1) echo "<th>Ações</th>"; ?>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while ($local = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($local['nome']) . "</td>";
                    echo "<td>" . htmlspecialchars($local['endereco']) . "</td>";
                    echo "<td>" . htmlspecialchars($local['faixa_preco']) . "</td>";
                    echo "<td>" . htmlspecialchars($local['esportes']) . "</td>";
                    
                    // Exibir botões de edição e exclusão apenas para admins
                    if ($nivel_acesso == 1) {
                        echo "<td>
                            <button><a href='editar.php?id={$local['id']}'>Editar</a></button>
                            <form method='POST' action='excluir.php' style='display:inline;' onsubmit='return confirm(\"Tem certeza que deseja excluir este local?\")'>
                                <input type='hidden' name='excluir_id' value='{$local['id']}'>
                                <button type='submit'>Excluir</button>
                            </form>
                        </td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Nenhum local encontrado.</td></tr>";
            }
            ?>
        </table>
    </div>

</body>
</html>
