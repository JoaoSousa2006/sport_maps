<?php
// --- CONEXAO AO BANCO ---
include_once("conexao.php");

// --- BUSCA / CONSULTA ---
$search = '';
if (!empty($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM tblPlaces WHERE 
            NamePlace LIKE '%$search%' OR 
            AdressPlace LIKE '%$search%' OR 
            EmailPlace LIKE '%$search%' OR 
            PhonePlace LIKE '%$search%' 
            ORDER BY idPlace DESC";
} else {
    $sql = "SELECT * FROM tblPlaces ORDER BY idPlace DESC";
}

$result = $conn->query($sql);

// --- EXCLUIR LOCAL ---
if (isset($_POST['excluir_id'])) {
    $idExcluir = intval($_POST['excluir_id']);
    $deleteSql = "DELETE FROM tblPlaces WHERE idPlace = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param('i', $idExcluir);
    $stmt->execute();

    header('Location: locais.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="style_lista.css">
  <title>Lista de Locais</title>
</head>
<body>
<div class="page-container">
  <header class="header">
    <span class="back__btn">
      <a href="index.html"><i class="ri-arrow-left-line"></i> Voltar</a>
    </span>
    <h1>Locais Cadastrados</h1>
  </header>

  <main class="content">
    <form method="GET">
      <input type="search" name="search" placeholder="Buscar locais..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="btn-search"><i class="ri-search-line"></i> Buscar</button>
    </form>

    <table class="places-table">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Endereço</th>
          <th>Email</th>
          <th>Telefone</th>
          <th>Preço</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['NamePlace']) ?></td>
            <td><?= htmlspecialchars($row['AdressPlace']) ?></td>
            <td><?= htmlspecialchars($row['EmailPlace']) ?></td>
            <td><?= htmlspecialchars($row['PhonePlace']) ?></td>
            <td><?= $row['PricePlace'] == 0 ? 'Gratuito' : 'R$ ' . number_format($row['PricePlace'], 2, ',', '.') ?></td>
            <td class="actions">
              <form method="GET" action="editar_local.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= $row['idPlace'] ?>">
                <button type="submit" class="btn edit"><i class="ri-edit-box-line"></i></button>
              </form>

              <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este local?');">
                <input type="hidden" name="excluir_id" value="<?= $row['idPlace'] ?>">
                <button type="submit" class="btn delete"><i class="ri-delete-bin-6-line"></i></button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
          <tr><td colspan="6" style="text-align:center;">Nenhum local encontrado.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </main>
</div>
</body>
</html>
