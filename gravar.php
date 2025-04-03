<?php
    // Conecta ao banco e verifica a conexão.
    include 'conexao.php';

    // Recebendo os dados do formulário
    $NamePlace = $_POST["txt_nome"];
    $AdressPlace = $_POST["txt_endereco"];
    $SpaceType = $_POST["txt_espaco"];
    $PriceRange = $_POST["txt_preco"];
    $SportType = isset($_POST["txt_atividades"]) ? implode(", ", $_POST["txt_atividades"]) : "";

    // Verifica se já existe o local no banco
    $sql = "SELECT * FROM locais WHERE NomeLocal = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $NamePlace);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<center><br>LOCAL JÁ CADASTRADO!!!<br><br></center>";
    } else {
        // Insere os dados no banco
        $sql = "INSERT INTO locais (NomeLocal, Endereco, Espaco, FaixaPreco, Atividades) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $NamePlace, $AdressPlace, $SpaceType, $PriceRange, $SportType);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<center><br>LOCAL CADASTRADO COM SUCESSO!!!<br><br></center>";
        } else {
            echo "Erro ao cadastrar: " . mysqli_error($conn);
        }
    }

    // Fecha a conexão
    mysqli_close($conn);
?>