<?php
session_start();
session_destroy(); // Destroi a sessão
header("Location: logcad.php"); // Redireciona para a página de login
exit();
?>
