<?php

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST["user"];
    $pass = $_POST['password'];
    $remember = $_POST['remember'];

    $errors = login($conn, $user, $pass, $remember);
}

?>

<section class="login-box">
    <div class="login-text">
        <h1>Login</h1>
    </div>
    <div class="form">
        <form action="login" method="POST">
            <input type="text" name="user" placeholder="Email ou senha:">
            <a href="#">Esqueceu sua senha?</a>
            <input type="password" name="password" placeholder="Senha:">

            <div class="checkbox">
                <input type="checkbox" name="remember" id="">
                <p>Lembrar senha</p>
            </div>
            
            <input type="submit" value="Login">
        </form>

        <a href="register" class="register">Ou crie uma conta clicando aqui</a>
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