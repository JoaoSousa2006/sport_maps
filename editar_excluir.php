<?php
session_start();
include_once("conexao.php");

// Verifica se o usuário é administrador
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] != 1) {
    die("Acesso negado.");
}
// Editar Local
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $espaco = $_POST['espaco'];
    $preco = $_POST['preco'];
    $atividades = implode(", ", $_POST['atividades']);

    $sql = "UPDATE locais SET Nome=?, Endereco=?, Espaco=?, Preco=?, Atividades=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $endereco, $espaco, $preco, $atividades, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Local atualizado com sucesso!'); window.location.href='locais.php';</script>";
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
    }
}

// Excluir Local
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM locais WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Local excluído com sucesso!'); window.location.href='locais.php';</script>";
    } else {
        echo "Erro ao excluir: " . $stmt->error;
    }
}
?>
