<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clone do Youtube</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/responsividade.css">
    
    <?php 
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';

        $cssPath = 'public/css/' . $page . '.css';
        
        if(file_exists($cssPath)) {
            echo "<link rel='stylesheet' href='$cssPath'>";
            if($page == 'home' || $page == 'watch') {
                echo "<link rel='stylesheet' href='public/css/header.css'>";
            }
        }
    ?>
</head>
<body>
<?php 
    if(file_exists($cssPath) && $page == 'home' || $page == 'watch') {
        echo "<header class='header'>
    <div class='logo'>
        <a href='home'><h1>PhTube</h1></a>
    </div>

    <div class='search'>
        <form action='pesquisa' method='post'>
            <button type='submit'>
                    <i class='fa fa-search'></i>
            </button>
            <input type='text' id='search' placeholder='Pesquisar'>
        </form>
    </div>

    <nav class='navbar'>
        <a href='upload'><i class='fa-solid fa-video'></i></a>
        <a href=''><i class='fa-regular fa-bell'></i></a>
        <a href=''><img src='public/imgs/perfil-default.png' alt=''></a>
    </nav>
    
</header>
";
    }
?>
