<?php
$videos = homeVideos($conn);
?>



<div class="sidebar">
    <nav>
        <ul>
            <li><a href="#"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="#"><i class="fas fa-bell"></i>Inscrições</a></li>
            <li><a href="#"><i class="fas fa-history"></i>Histórico</a></li>
            <li><a href="#"><i class="fas fa-video"></i>Seus vídeos</a></li>
            <li><a href="#"><i class="fas fa-clock"></i>Assistir mais tarde</a></li>
            <li><a href="#"><i class="fas fa-thumbs-up"></i>Vídeos curtidos</a></li>
        </ul>
    </nav>
</div>

<div class="content-area">
    <h2 class="section-title">Vídeos recomendados</h2>
    <div class="video-grid">
        <?php
        // Recuperar vídeos do banco de dados
        $videos = homeVideos($conn);

        // Verificar se há vídeos
        if (!empty($videos)) {
            foreach ($videos as $video) {
                // Extrair informações do vídeo
                $id = $video['id'];
                $titulo = htmlspecialchars($video['titulo']); // Para evitar XSS
                $canal = htmlspecialchars($video['canal']);
                $visualizacoes = htmlspecialchars($video['visualizacoes']);
                $dataUpload = date('d/m/Y', strtotime($video['data_upload']));
                $duracao = htmlspecialchars($video['duracao']); // Supondo que você tenha essa coluna

                echo "
                <div class='video-item'>
                    <a href='watch?v=$id'>
                        <div class='video-thumbnail'>
                            <img src='{$video['caminho_arquivo']}' alt='$titulo' class='thumbnail-img'>
                            <div class='play-button'></div>
                        </div>
                        <h3 class='video-title'>$titulo</h3>
                        <p class='video-info'>$canal • $visualizacoes visualizações • $dataUpload • $duracao</p>
                    </a>
                </div>
                ";
            }
        } else {
            echo "<p>Nenhum vídeo encontrado.</p>";
        }
        ?>
    </div>
</div>