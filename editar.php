<?php
session_start();
include_once("conexao.php");

// Verifica se o usuário é um administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href = 'locais.php';</script>";
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Busca os dados do local a ser editado
    $sql = "SELECT * FROM locais WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $local = $result->fetch_assoc();
    } else {
        echo "<script>alert('Local não encontrado!'); window.location.href = 'locais.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID inválido!'); window.location.href = 'locais.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $espaco = $_POST['espaco'];
    $preco = $_POST['preco'];
    $atividades = implode(", ", $_POST['atividades']);
    
    $sql = "UPDATE locais SET nome=?, endereco=?, espaco=?, preco=?, atividades=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $endereco, $espaco, $preco, $atividades, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Local atualizado com sucesso!'); window.location.href = 'locais.php';</script>";
    } else {
        echo "Erro ao atualizar local: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Local</title>
</head>
<body>
    <h1>Editar Local</h1>
    <form method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo $local['nome']; ?>" required><br>
        
        <label>Endereço:</label>
        <input type="text" name="endereco" value="<?php echo $local['endereco']; ?>" required><br>
        
        <label>Espaço:</label>
        <select name="espaco">
            <option value="ArLivre" <?php if ($local['espaco'] == 'ArLivre') echo 'selected'; ?>>Ao ar livre</option>
            <option value="Coberto" <?php if ($local['espaco'] == 'Coberto') echo 'selected'; ?>>Área coberta</option>
            <option value="Misto" <?php if ($local['espaco'] == 'Misto') echo 'selected'; ?>>Mista</option>
        </select><br>
        
        <label>Faixa de Preço:</label>
        <input type="text" name="preco" value="<?php echo $local['preco']; ?>" required><br>
        
        <label>Atividades:</label><br>
        <?php
        $atividades = ["Ciclismo", "Skate", "Patinação", "Corrida", "Quadra"];
        $atividades_selecionadas = explode(", ", $local['atividades']);
        foreach ($atividades as $atividade) {
            $checked = in_array($atividade, $atividades_selecionadas) ? "checked" : "";
            echo "<input type='checkbox' name='atividades[]' value='$atividade' $checked> $atividade <br>";
        }
        ?>
        
        <button type="submit">Salvar</button>
    </form>
    <a href="locais.php">Voltar</a>
</body>
</html>
