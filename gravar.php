<?php
    //Conecta ao banco e verifica a conexão.
    include 'conexao.php';

    //Recebendo os dados do formulário
    $NamePlace = $_POST["txt_nome"];
    $AdressPlace = $_POST["txt_endereco"];
    $ = $_POST["txt_espaco"];
    $ = $_POST["txt_preco"];
    $SportType = $_POST["txt_atividades"];

    // Criando a conexão com o banco de dados
    $conn = mysqli_connect("localhost", "root", "usbw", "");

    if (!$conn) {
        die("Falha na conexão: " . mysqli_connect_error());
    }

    //Verifica se já existe o funcionário no banco
    $sql = "SELECT * FROM  WHERE  = ? OR  = ?";
    $stmt = mysqli_prepare($conn, $sql); //Prepara uma consulta SQL para execução segura, prevenindo SQL Injection
    mysqli_stmt_bind_param($stmt); //Associa valores às variáveis da consulta preparada
    //"ss" -> Define os tipos dos parâmetros
    mysqli_stmt_execute($stmt); //Executa a consulta preparada, substituindo os placeholders (?) pelos valores vinculados
    $result = mysqli_stmt_get_result($stmt); //Obtém o resultado da consulta SQL executada e armazena em uma variável

    //Verifica o número de linhas
    if (mysqli_num_rows($result) > 0) {
        echo "<center><br>LOCAL JÁ CADASTRADO!!!<br><br></center>";
    } else {
        //Insere os dados no banco
        $sql = "INSERT INTO  () 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt);
        //"d" garante que o dado é do tipo double
        if (mysqli_stmt_execute($stmt)) {
            echo "<center><br>LOCAL CADASTRADO COM SUCESSO!!!<br><br></center>";
        } else {
            echo "Erro ao cadastrar: " . mysqli_error($conn);
        }
    }

    //Fecha a conexão
    mysqli_close($conn);
?>
