<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="style_lista.css" />
    <title>Lista de Locais</title>
  </head>
  <body>
    <div class="page-container">
      <header class="header">
        <span class="back__btn">
            <a href="index.html"><i class="ri-arrow-left-line"></i></a>
        </span>
        <h1>Locais Cadastrados</h1>
      </header>

      <main class="content">
        <input type="search" placeholder="Buscar locais..." />

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
             <?php include 'listar_locais.php'; ?>
          </tbody>
        </table>
      </main>
    </div>
  </body>
</html>
