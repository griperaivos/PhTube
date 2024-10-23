<?php

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $video = $_FILES['video'] ?? '';

    upload($conn, $title, $description, $video);
}

?>

    <h2>Faça upload do seu vídeo</h2>
    <form action="upload" method="POST" enctype="multipart/form-data">
        <label for="title">Título:</label>
        <input type="text" name="title" required><br><br>

        <label for="description">Descrição:</label>
        <textarea name="description" required></textarea><br><br>

        <label for="video">Selecione o vídeo:</label>
        <input type="file" name="video" accept="video/*" required><br><br>

        <input type="submit" value="Fazer Upload">
    </form>