<?php
// Incluir configurações e funções essenciais
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

session_start();

// Obter a página solicitada a partir da URL amigável
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if($page === 'login' || $page === 'register') {
    if(isset($_COOKIE['remember_me'])) {
        $user = $_COOKIE['remember_me'];

        $stmt = $conn->prepare('SELECT COUNT(*) FROM usuarios WHERE nome = ? OR email = ?');
        $stmt->bind_param('ss', $user, $user);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        if($count > 0) {
            $_SESSION['user'] = $user;
            header('Location: home');
        } else {
            // O cookie existe, mas o usuário não é mais válido
            setcookie('remember_me', '', time() - 3600, "/"); // Remove o cookie
        }
    }
}

// Definir o caminho completo do arquivo de visualização
$viewPath = 'views/' . $page . '.php';

// Verificar se o arquivo existe e incluir, caso contrário, mostrar página 404
if (file_exists($viewPath)) {
    require_once $viewPath;
} else {
    echo "<h1>404 - Página Não Encontrada</h1>";
}

require_once 'includes/footer.php';
?>
