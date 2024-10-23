<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
if (typeof initComments === 'undefined') {
        console.log("entrou no arquivo do ajax");
        
        function initComments() {
            // Pega o parâmetro 'v' da URL atual
            var urlParams = new URLSearchParams(window.location.search);
            var videoId = urlParams.get('v'); // Pega o valor do parâmetro 'v'
            console.log("Video ID (v):", videoId); // Debug
            
            var usuarioAtual = <?php echo json_encode($_SESSION['nome'] ?? 'Anônimo'); ?>;
            var isLoading = false;

            function carregarComentarios() {
                if (isLoading || !videoId) return;
                
                console.log("entrou no carregar comentarios");
                isLoading = true;
                
                $.ajax({
                    url: 'watch',
                    type: 'POST',
                    data: { 
                        action: 'carregar_comentarios', 
                        video_id: videoId  // Usando o parâmetro 'v'
                    },
                    success: function(data) {
                        $('#comentarios').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao carregar comentários:", error);
                        $('#comentarios').html('<div class="comment">Erro ao carregar comentários.</div>');
                    },
                    complete: function() {
                        isLoading = false;
                    }
                });
            }

            // Event handler para submeter comentários
            $('.comment-submit').off('click').on('click', function(e) {
                e.preventDefault();
                console.log("apertou o botao");
                
                var comentario = $('.comment-input').val();

                if (comentario.trim() !== '') {
                    $.ajax({
                        url: 'watch',
                        type: 'POST',
                        data: { 
                            action: 'inserir_comentario', 
                            video_id: videoId,  // Usando o parâmetro 'v'
                            usuario: usuarioAtual, 
                            comentario: comentario 
                        },
                        success: function(response) {
                            $('.comment-input').val('');
                            carregarComentarios();
                        },
                        error: function(xhr, status, error) {
                            console.error("Erro ao inserir comentário:", error);
                            alert('Erro ao enviar comentário. Por favor, tente novamente.');
                        }
                    });
                } else {
                    alert('Por favor, digite um comentário.');
                }
            });

            // Carregar comentários iniciais
            carregarComentarios();
        }

        $(document).ready(function() {
            initComments();
        });
    }
</script>
</body>
</html>