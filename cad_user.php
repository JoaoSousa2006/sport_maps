<?php
include_once('conexao.php');

// Inicializa a mensagem e o tipo de mensagem
$message = "";
$messageType = ""; // 'success' ou 'error'
$redirectUrl = "";
$redirectDelay = 3; // Redireciona após 3 segundos

$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Usar prepared statements para verificar se o usuário já existe
$stmtCheck = $connection->prepare("SELECT COUNT(*) FROM tblusers WHERE NameUser = ? OR EmailUser = ?");
$stmtCheck->bind_param("ss", $username, $email);
$stmtCheck->execute();
$stmtCheck->bind_result($count);
$stmtCheck->fetch();
$stmtCheck->close();

if ($count > 0) {
    $message = "USUÁRIO JÁ CADASTRADO!!! O nome de usuário ou e-mail já está em uso.";
    $messageType = "error";
    $redirectUrl = "cadUser.html"; // Redireciona para a página de cadastro de usuário
} else {
    // Inserir novo usuário
    $sqlInsert = "INSERT INTO `tblusers` (`idUser`, `NameUser`, `PasswordUser`, `EmailUser`) VALUES (NULL, ?, ?, ?)";
    $stmtInsert = $connection->prepare($sqlInsert);

    if ($stmtInsert === false) {
        $message = "Erro interno ao preparar o cadastro: " . $connection->error;
        $messageType = "error";
        $redirectUrl = "cadUser.html";
    } else {
        // 'sss' para os tipos dos parâmetros (username, password, email)
        $stmtInsert->bind_param("sss", $username, $password, $email);

        if ($stmtInsert->execute()) {
            $message = "USUÁRIO CADASTRADO COM SUCESSO!!!";
            $messageType = "success";
            $redirectUrl = "index.html"; // Redireciona para a página inicial após sucesso
        } else {
            $message = "ERRO AO CADASTRAR USUÁRIO: " . $stmtInsert->error;
            $messageType = "error";
            $redirectUrl = "cadUser.html";
        }
        $stmtInsert->close();
    }
}
$connection->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="refresh" content="<?php echo $redirectDelay; ?>;url=<?php echo $redirectUrl; ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="root.css" />
    <link rel="stylesheet" href="styleCad.css" />
    <title>Processando Cadastro de Usuário</title>
    <style>
        /* Estilo para a mensagem centralizada */
        .message-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            flex-direction: column;
        }
        .message-box {
            background-color: var(--yellow-glow);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 90%;
        }
        .message-box h3 {
            color: var(--text-dark);
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        .message-box p {
            color: var(--text-light);
            font-size: 1.1rem;
        }
        .message-box.error h3 {
            color: var(--corrupted-oil); /* Uma cor de erro mais forte do seu palette */
        }
        .message-box.success h3 {
            color: var(--primary-color-dark); /* Cor de sucesso do seu palette */
        }
        .message-box a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: var(--primary-color-dark);
            color: var(--extra-light);
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .message-box a:hover {
            background-color: var(--uzi-hoodie);
        }
    </style>
</head>
<body>
    <div class="container message-container">
        <div class="message-box <?php echo $messageType; ?>">
            <h3 class="form__title"><?php echo $message; ?></h3>
            <p>Você será redirecionado em <?php echo $redirectDelay; ?> segundos...</p>
            <a href="<?php echo $redirectUrl; ?>">Ou clique aqui para ir agora</a>
        </div>
    </div>
</body>
</html>