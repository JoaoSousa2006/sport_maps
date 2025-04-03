<?php
//Conecta ao banco e verifica a conexão.
include_once("conexao.php");
session_start();

// Verifica se o usuário é administrador
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// Verifica se foi feita uma pesquisa
$search_query = "";
if (!empty($_GET['search'])) {
    $data = $_GET['search'];
    $search_query = "WHERE Nome LIKE '%$data%' OR Endereco LIKE '%$data%' OR Preco LIKE '%$data%' OR Atividades LIKE '%$data%'";
}

//Consulta SQL com todos os locais
$sql = "SELECT * FROM locais $search_query ORDER BY id DESC;";
$result = $conn->query($sql);
?><!DOCTYPE html><html lang="pt-br">
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Locais</h1>
        <form method="GET">
            <input type="text" name="search" placeholder="Buscar local..." >
            <button type="submit">Buscar</button>
        </form>
        <table>
            <tr>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Espaço</th>
                <th>Faixa de Preço</th>
                <th>Atividades</th>
                <?php if ($is_admin) echo '<th>Ações</th>'; ?>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['Nome']}</td>";
                    echo "<td>{$row['Endereco']}</td>";
                    echo "<td>{$row['Espaco']}</td>";
                    echo "<td>{$row['Preco']}</td>";
                    echo "<td>{$row['Atividades']}</td>";
                    if ($is_admin) {
                        echo "<td>
                            <a href='editar_excluir.php?edit={$row['id']}'>Editar</a> |
                            <a href='editar_excluir.php?delete={$row['id']}' onclick='return confirm("Tem certeza que deseja excluir este local?")'>Excluir</a>
                        </td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum local encontrado.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>