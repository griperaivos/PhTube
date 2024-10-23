<?php
// Incluir o arquivo de funções
require_once 'includes/functions.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass1 = $_POST['password1'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    // Chama a função register e armazena os erros
    $errors = register($conn, $username, $email, $pass1, $pass2);

}
?>

    <section class="login-box">
        <div class="login-text">
            <h1>Crie sua Conta</h1>
        </div>
        <div class="form">

            <form action="register" method="POST">
                <input type="text" name="username" placeholder="insira seu nome de usuario">
                <input type="email" name="email" placeholder="Email:" value="<?php echo htmlspecialchars($email); ?>">
                <input type="password" name="password1" placeholder="Senha:">
                <input type="password" name="password2" placeholder="Confirme sua Senha:">
                <input type="submit" value="Criar">
            </form>
            <a href="login" class="register">Já tem uma conta? clique aqui</a>
        </div>
    </section>
    <?php
            if (!empty($errors)) {
                echo '<ul class="error-list">';
                foreach ($errors as $error) {
                    echo "<li style='color: red;'>" . htmlspecialchars($error) . "</li>";
                }
                echo '</ul>';
            }
            ?>
