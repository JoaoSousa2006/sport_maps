<?php
if (isset($_POST['submit'])) {
    include_once ('conexao.php');

    $name = $_POST['compname'];
    $email = $_POST['username']; // Mantendo 'username' no formulário, mas o campo é EmailUser
    $password = $_POST['password'];
    
    // Inserção com os nomes corretos do banco de dados
    $result = mysqli_query($connection, "INSERT INTO tblUsers (NameUser, EmailUser, PasswordUser) VALUES ('$name', '$email', '$password')");
    
    header('location:logcad.php');
}
?><!doctype html>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
    <link rel="stylesheet" href="logcad.css">
    <style>
        .container {
            width: 350px;
            perspective: 1000px;
            margin: auto;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .card {
            width: 100%;
            height: 400px;
            transform-style: preserve-3d;
            transition: transform 0.6s;
        }
        .card.flipped {
            transform: rotateY(180deg);
        }
        .card-side {
            width: 100%;
            height: 100%;
            position: absolute;
            backface-visibility: hidden;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            padding: 20px;
        }
        .card-back {
            transform: rotateY(180deg);
        }
        .toggle-btn {
            cursor: pointer;
            text-decoration: underline;
            color: blue;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card" id="card">
            <div class="card-side card-front w3-center w3-padding">
                <h4>Log In</h4>
                <form action="test_login.php" method="post">
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="username" placeholder="E-mail" required>
                    <input class="w3-input w3-border" type="password" name="password" placeholder="Senha" required><br>
                    <input type="checkbox" id="adm" name="adm" value="1"><label for="adm">Sou administrador</label><br>
                    <button class="w3-button w3-blue w3-margin-top" type="submit">Entrar</button>
                </form>
                <p class="w3-margin-top">Não tem conta? <span class="toggle-btn" onclick="flipCard()">Cadastre-se</span></p>
            </div>
            <div class="card-side card-back w3-center w3-padding">
                <h4>Sign Up</h4>
                <form action="logcad.php" method="post">
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="compname" placeholder="Nome Completo" required>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="username" placeholder="E-mail" required>
                    <input class="w3-input w3-border" type="password" name="password" placeholder="Senha" required>
                    <button class="w3-button w3-green w3-margin-top" type="submit">Cadastrar</button>
                </form>
                <p class="w3-margin-top">Já tem conta? <span class="toggle-btn" onclick="flipCard()">Entre</span></p>
            </div>
        </div>
    </div><script>
    function flipCard() {
        document.getElementById('card').classList.toggle('flipped');
    }
</script>

</body>
</html>