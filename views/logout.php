<?php
session_start();

// Remover todas as variáveis de sessão
session_unset();

// Destruir a sessão
session_destroy();

// Remover o cookie de "remember me", se existir
if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, "/"); // Expira o cookie
}

// Redirecionar para a página de login (ou qualquer página desejada)
header("Location: login");
exit();
?>
