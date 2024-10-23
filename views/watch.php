
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se for uma requisição POST, usamos o video_id enviado
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'inserir_comentario') {
            $video_id = $_POST['video_id']; // Este é o valor de 'v'
            $usuario = $_POST['usuario'];
            $comentario = $_POST['comentario'];
            inserirComentario($conn, $video_id, $usuario, $comentario);
            exit;
        } elseif ($_POST['action'] === 'carregar_comentarios') {
            $video_id = $_POST['video_id']; // Este é o valor de 'v'
            $comentarios = carregarComentarios($conn, $video_id);

            if (!empty($comentarios)) {
                foreach ($comentarios as $comentario) {
                    ?>
                    <div class="comentario">
                        <div class="com">
                        <div class="comment-avatar"></div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-username"><?php echo htmlspecialchars($comentario['usuario']); ?></span>
                                <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comentario['data'])); ?></span>
                            </div>
                            <p class="comment-text"><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                            <div class="comment-actions">
                                <span class="comment-action">Gostei</span>
                                <span class="comment-action">Responder</span>
                            </div>
                        </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='comment'>Nenhum comentário ainda.</div>";
            }
            exit;
        }
    }
} else {
    // Se for GET, pegamos o 'v' da URL
    if (!isset($_GET['v'])) {
        header('Location: index.php');
        exit;
    }
    $video = video($conn, $_GET['v']);
}
?>

?>

<main class="main-content">
        <div class="video-column">
            <div class="video-player">
            <video width="100%" controls>
                <source src="public/uploads/videos/<?php echo $video['file']; ?>" type="video/mp4">
                Seu navegador não suporta o elemento de vídeo.
            </video>
            </div>
            <h1 class="video-title"><?php echo $video['title']; ?></h1>
            <p class="video-stats"><?php echo $video['views']; ?> visualizações • <?php echo $video['data_upload']; ?></p>
            <div class="video-actions">
                <button class="action-button"><i class="fa fa-thumbs-up" aria-hidden="true"></i>Gostei</button>
                <button class="action-button"><i class="fa-solid fa-thumbs-down"></i> Não gostei</button>
                <button class="action-button"><i class="fa-solid fa-share"></i> Compartilhar</button>
                <button class="action-button"><i class="fas fa-save"></i> Salvar</button>
            </div>
            <div class="channel-info">
                <div class="channel-avatar"></div>
                <div class="channel-details">
                    <h2 class="channel-name">Nome do Canal</h2>
                    <p class="subscriber-count">1M inscritos</p>
                </div>
                <button class="subscribe-button">INSCREVER-SE</button>
            </div>
            <p class="video-description">
            <?php echo $video['description']; ?>
            </p>
            <span class="show-more">MOSTRAR MAIS</span>
            <div class="comments-section">
                <h3 class="comments-title">Comentários</h3>
                <div class="add-comment">
                    <div class="user-avatar"></div>
                    <div class="comment-input-wrapper">
                        <input type="text" class="comment-input" placeholder="Adicione um comentário...">
                        <div class="comment-actions">
                            <button class="comment-cancel">Cancelar</button>
                            <button class="comment-submit">Comentar</button>
                        </div>
                    </div>
                </div>
                <div id="comentarios">
                    <!-- Comentários serão carregados aqui -->
                </div>
            </div>
        </div>
        <div class="suggestions-column">
            <h3 class="suggestions-title">Próximos vídeos</h3>
            <div class="suggested-video">
                <div class="suggested-thumbnail"></div>
                <div class="suggested-info">
                    <h4 class="suggested-title">Título do vídeo sugerido que pode ser longo</h4>
                    <p class="suggested-channel">Nome do Canal</p>
                    <p class="suggested-views">100K visualizações • 2 dias atrás</p>
                </div>
            </div>
            <!-- Adicione mais vídeos sugeridos aqui -->
        </div>
    </main>