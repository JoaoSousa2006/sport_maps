<?php
session_start();
//  print_r($_SESSION);
//  print_r('<br>');
include_once ('conexao.php');

if ((!isset($_SESSION['username']) == true) and (!isset($_SESSION['password']) == true)) {
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['nivel_acesso']);
    header(header: 'location:login.html');

    //Teste de funcionamento sessão abaixo
    //  print_r('<br>');
    //  print_r('não há sessão ativa');
    //  print_r('<br>');
} else {
    $logged = $_SESSION['username'];
    $nivel_acesso = $_SESSION['nivel_acesso'];

    // Teste de funcionamento sessão abaixo
    // print_r('<br>');
    // print_r('há sessão ativa: ');
    // print_r($logged);
  }
    ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="style_index.css" />
    <title>Web Design Mastery | SoulTravel</title>
  </head>
  <body>
    <nav>
      <div class="nav__header">
        <div class="nav__logo">
          <a href="">Sport<span>Maps</span>.</a>
        </div>
        <div class="nav__menu__btn" id="menu-btn">
          <span><i class="ri-menu-line"></i></span>
        </div>
      </div>
      <ul class="nav__links" id="nav-links">
          <li><a href="cad_local.html">Adicionar Local</a></li>
        <li><a href="lista_locais.php">Analisar Locais</a></li>
      </ul>
      <div class="nav__btns">
        <!-- <a href="" class="btn sign__up">Sign Up</a> -->
        <!-- <a  href="login_signin.php" class="btn sign__in">Logar e cadastrar-se</a> -->
        <!-- <a href="cadUser.html" class="btn sign__up">Cadastro</a> -->
        <a href="unLog.php" class="btn sign__in">Sair</a>

      </div>
    </nav>
    <header class="header__container">
      <!-- <div class="header__image">
        <div class="header__image__card header__image__card-1">
          
        </div>
        <div class="header__image__card header__image__card-2">
          
        </div>
        <div class="header__image__card header__image__card-3">
          
        </div>
        <div class="header__image__card header__image__card-4">
          
        </div>

      </div> -->
      <div class="header__content">
        <h1>Bem Vind* <?php echo $_SESSION['username']; ?></h1>
        <p style="max-width: 85%;">
          Aqui é o local reservado exclusivamente para os usuários do site. 
        </p>
        <!-- <form action="/">
          <div class="input__row">
            <div class="input__group">
              <h5>Destination</h5>
              <div>
                <span><i class="ri-map-pin-line"></i></span>
                <input type="text" placeholder="Paris, France" />
              </div>
            </div>
            <div class="input__group">
              <h5>Date</h5>
              <div>
                <span><i class="ri-calendar-2-line"></i></span>
                <input type="text" placeholder="17 July 2024" />
              </div>
            </div>
          </div>
          <button type="submit">Search</button>
        </form> -->
    </div>
    </header>
      </body>
</html>
