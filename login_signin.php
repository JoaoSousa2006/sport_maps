<?php
session_start();
include_once('conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["username"];
    $password = $_POST["password"];

    if (isset($_POST['compname'])) {
        // Cadastro de usuário
        $name = $_POST["compname"];
        $userType = isset($_POST["adm"]) ? "admin" : "normal";

        $sql = "INSERT INTO tblUsers (NameUser, EmailUser, PasswordUser, UserType) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $password, $userType);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["user_id"] = mysqli_insert_id($conn);
            $_SESSION["user_email"] = $email;
            $_SESSION["user_type"] = $userType;

            header("Location: locais.php");
            exit();
        } else {
            echo "<script>alert('Erro ao cadastrar!');</script>";
        }
    } else {
        // Login de usuário
        $sql = "SELECT * FROM tblUsers WHERE EmailUser = ? AND PasswordUser = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            $_SESSION["user_id"] = $user["idUser"];
            $_SESSION["user_email"] = $user["EmailUser"];
            $_SESSION["user_type"] = $user["UserType"];

            header("Location: locais.php");
            exit();
        } else {
            echo "<script>alert('Email ou senha incorretos!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
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
                <form action="logcad.php" method="post">
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="username" placeholder="E-mail" required>
                    <input class="w3-input w3-border" type="password" name="password" placeholder="Senha" required><br>
                    <button class="w3-button w3-blue w3-margin-top" type="submit">Entrar</button>
                </form>
                <p class="w3-margin-top">Não tem conta? <span class="toggle-btn" onclick="flipCard()">Cadastre-se</span></p>
            </div>
            <div class="card-side card-back w3-center w3-padding">
                <h4>Sign Up</h4>
                <form action="logcad.php" method="post">
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="compname" placeholder="Nome Completo" required>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="username" placeholder="E-mail" required>
                    <input class="w3-input w3-border" type="password" name="password" placeholder="Senha" required><br>
                    <input type="checkbox" id="adm" name="adm" value="1"><label for="adm">Sou administrador</label><br>
                    <button class="w3-button w3-green w3-margin-top" type="submit">Cadastrar</button>
                </form>
                <p class="w3-margin-top">Já tem conta? <span class="toggle-btn" onclick="flipCard()">Entre</span></p>
            </div>
        </div>
    </div>

    <script>
        function flipCard() {
            document.getElementById('card').classList.toggle('flipped');
        }
    </script>
</body>
</html>
