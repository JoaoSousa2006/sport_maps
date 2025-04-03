<?php

//Conecta ao banco e verifica a conexão.
include_once("conexao.php");

// Verifica se foi feita uma pesquisa

if (!empty($_GET['search'])) {
    $data = $_GET['search'];
    //Consulta SQL por número de registro ou nome
    $sql = "SELECT * FROM tb_funcionarios WHERE N_Registro like '%$data%' or Nome_Funcionario LIKE '%$data%' ORDER BY N_Registro DESC;";
} else {
    //Consulta SQL com todos os funcionarios
    $sql = "SELECT * FROM tb_funcionarios ORDER BY N_Registro DESC;";
}
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
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 0px 10px gray;
            border-radius: 8px;
        }
        input, select {
            padding: 10px;
            margin: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        a {
            color: #fff;
        }
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
                <th>Local</th>
                <th>Endereço</th>
                <th>Espaço</th>
                <th>Faixa de Preço</th>
                <th>Atividades</th>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while ($user_data = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td class='text-center'>" . str_pad($user_data['N_Registro'], 5, '0', STR_PAD_LEFT) . "</td>";
                    // Formata o número de registro com zeros à esquerda
                        //str_pad($input, $length, $pad_string, $pad_type):
                        //$input: O valor que você deseja formatar ($user_data['N_Registro']).
                        //$length: O comprimento total desejado (5 caracteres).
                        //$pad_string: O caractere usado para preencher ('0').
                        //$pad_type: Define onde os caracteres serão adicionados. STR_PAD_LEFT adiciona os zeros à esquerda.
                    echo "<td class='text-center'>" . $user_data['Nome_Funcionario'] . "</td>";
                    //Formata a data para dd/mm/aa
                    $dataFormatada = DateTime::createFromFormat('Y-m-d', $user_data['data_admissao'])->format('d/m/Y');
                    echo "<td class='text-center'>" . $dataFormatada . "</td>";
                    echo "<td class='text-center'>" . $user_data['cargo'] . "</td>";
                    // Formata os valores numéricos como moeda
                    echo "<td class='text-center'>R$ " . number_format($user_data['salario_bruto'], 2, ',', '.') . "</td>";
                    echo "<td class='text-center'>R$ " . number_format($user_data['inss'], 2, ',', '.') . "</td>";
                    echo "<td class='text-center'>R$ " . number_format($user_data['salario_liquido'], 2, ',', '.') . "</td>";
                    echo "<td>
                        <button><a style='text-decoration:none' href='editar.php?N_Registro=$user_data[N_Registro]'>Editar</a></button>
                        <br><br>
                    <form method='POST' style='display:inline;' onsubmit='return confirm(\"Tem certeza que deseja excluir este funcionário?\")'>
                        <input type='hidden' name='excluir_id' value='" . $user_data['N_Registro'] . "'>
                        <button type='submit'>Excluir</button>
                    </form>
                    
                  </td>";
                }
            } else {
                echo "<tr><td colspan='7'>Nenhum funcionário encontrado.</td></tr>";
            }
            ?>
        </table>
    </div>

</body>