<?php
// Configurando banco de dados
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'youtube_clone';

// Criando a conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $db);

// Verificando a conexão
if ($conn->connect_error) {
    die("erro de conexão: " . $conn->connect_error);
}
?>