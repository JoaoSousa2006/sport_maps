<?php
// Conexão com o banco
$host = 'localhost';
$user = 'root'; // ajuste se necessário
$pass = '';     // ajuste se necessário
$dbname = 'sport_maps';

$conn = new mysqli($host, $user, $pass, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Consulta para buscar os locais
$sql = "SELECT NamePlace, AdressPlace, EmailPlace, PhonePlace, PricePlace FROM tblPlaces";
$result = $conn->query($sql);

// Gera as linhas da tabela
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['NamePlace']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AdressPlace']) . "</td>";
        echo "<td>" . htmlspecialchars($row['EmailPlace']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PhonePlace']) . "</td>";
        
        // Formatar preço
        $preco = $row['PricePlace'];
        if ($preco == 0) {
            echo "<td>Gratuito</td>";
        } else {
            echo "<td>R$ " . number_format($preco, 2, ',', '.') . "</td>";
        }

        // Botões
        echo "<td class='actions'>";
        echo "<button class='btn edit'><i class='ri-edit-box-line'></i></button>";
        echo "<button class='btn delete'><i class='ri-delete-bin-6-line'></i></button>";
        echo "</td>";
        
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>Nenhum local cadastrado.</td></tr>";
}

$conn->close();
?>
