<?php
session_start();

// Conecta ao banco
include_once("conexao.php");

// Verifica se foi feita uma busca
$search = '';
if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
}

$sql = "SELECT * FROM tblPlaces WHERE NamePlace LIKE '%$search%' OR AdressPlace LIKE '%$search%'";
$result = $conn->query($sql);

// Verifica o nivel_acesso
$isAdmin = isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 1;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="style_lista.css">
    <title>Lista de Locais</title>
</head>
<body>
    <div class="page-container">
        <header class="header">
            <span class="back__btn">
                <a href="index.php"><i class="ri-arrow-left-line"></i></a>
            </span>
            <h1>Locais Cadastrados</h1>
        </header>

        <main class="content">
            <form method="GET">
                <input type="search" name="search" placeholder="Buscar locais..." value="<?= htmlspecialchars($search) ?>">
                <button class="submit_btn" type="submit">Buscar</button>
            </form>

            <table class="places-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Endereço</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Preço</th>
                        <?php if ($isAdmin) echo '<th>Ações</th>'; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($place = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($place['NamePlace']) ?></td>
                            <td><?= htmlspecialchars($place['AdressPlace']) ?></td>
                            <td><?= htmlspecialchars($place['EmailPlace']) ?></td>
                            <td><?= htmlspecialchars($place['PhonePlace']) ?></td>
                            <td>
                                <?= $place['PricePlace'] ? "R$ ".number_format($place['PricePlace'],2,',','.') : "Gratuito" ?>
                            </td>
                            <?php if ($isAdmin): ?>
                            <td class="actions">
                                <a href="editar_local.php?id=<?= $place['idPlace'] ?>" class="btn edit"><i class="ri-edit-box-line"></i></a>
                                <a href="excluir_local.php?id=<?= $place['idPlace'] ?>" class="btn delete" onclick="return confirm('Tem certeza que deseja excluir este local?')"><i class="ri-delete-bin-6-line"></i></a>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
