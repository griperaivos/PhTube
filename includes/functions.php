<?php
function register($conn, $user, $email, $pass1, $pass2) {
    $errors = [];
    
    // Validação do email
    if (empty($user) || empty($email) || empty($pass1) || empty($pass2)) {
        $errors[] = 'O campo de nome de usuario/email/senha não pode estar vazio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Por favor, insira um email válido.';
    }
      elseif (strlen($pass1) < 6 || strlen($pass2) < 6) {
        $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif (emailExists($conn, $email)) {
        $errors[] = 'Este email já está registrado';
    } elseif (userExists($conn, $user)) {
        $errors[] = 'Este usuario já está registrado';
    }
    
    // Confirmação da senha
    if ($pass1 !== $pass2) {
        $errors[] = 'As senhas não coincidem.';
    }
    
    if(empty($errors)) {
        $passwordHash = password_hash($pass1, PASSWORD_DEFAULT);

        $stmt = $conn->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
        
        if($stmt) {
            $stmt->bind_param('sss', $user, $email, $passwordHash);

            if($stmt->execute()) {
                echo 'Usuario cadastrado com sucesso';
            } else {
                echo "erro ao cadastrar usuario: " . $stmt->error;
            }
        }
    }

    return $errors;
}

function login($conn, $user, $pass, $remember_me){
    $errors = [];

    if (!userExists($conn, $user) && !emailExists($conn, $user)) {
        $errors[] = 'Usuário ou senha incorretos.';
    } else {
        $stmt = $conn->prepare('SELECT id, senha, nome FROM usuarios WHERE nome = ? OR email = ?');

        if($stmt) {
            $stmt->bind_param('ss', $user, $user);
            $stmt->execute();

            $stmt->bind_result($userId, $passwordHash, $nome);
            $stmt->fetch();

            if(!password_verify($pass, $passwordHash)) {
                $errors[] = 'Usuário ou senha incorretos.';
            } else {

                $_SESSION['user'] = $user;
                $_SESSION['user_id'] = $userId;
                $_SESSION['nome'] = $nome;

                if ($remember_me) {
                    // Crie um cookie para lembrar o usuário
                    $cookieName = 'remember_me';
                    $cookieValue = $user; // ou você pode usar um ID único
                    $cookieExpire = time() + (86400 * 30); // 30 dias
    
                    setcookie($cookieName, $cookieValue, $cookieExpire, "/"); // O "/" indica que o cookie está disponível em todo o domínio
                }

                echo 'Login realizado com sucesso! Bem-vindo, ' . $user . '.';
                header('Location: home');
                exit(); 
            }
        } else {
            $errors[] = 'Erro ao verificar usuário: ' . $stmt->error;
        }
    }

    return $errors;
}

function emailExists($conn, $email){
    $stmt = $conn->prepare('SELECT COUNT(*) FROM usuarios WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $stmt->bind_result($count);
    $stmt->fetch();

    return $count > 0;
}

function userExists($conn, $user){
    $stmt = $conn->prepare('SELECT COUNT(*) FROM usuarios WHERE nome = ?');
    $stmt->bind_param('s', $user);
    $stmt->execute();

    $stmt->bind_result($count);
    $stmt->fetch();

    return $count > 0;
}

function upload($conn, $title, $description, $video) {
    // Diretório para salvar o vídeo
    $uploadDir = 'public/uploads/videos/';

    // Verifica se o usuário está logado
    if (!isset($_SESSION['user_id'])) {
        echo 'Você precisa estar logado para fazer o upload.';
        return;
    }

    // ID do usuário logado
    $userId = $_SESSION['user_id'];

    // Verifica se o vídeo foi enviado sem erros
    if ($video['error'] === UPLOAD_ERR_OK) {
        // Gera um nome único para o arquivo
        $extensao = pathinfo($video['name'], PATHINFO_EXTENSION);
        $videoName = uniqid() . '.' . $extensao;
        $destino = $uploadDir . $videoName;

        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($video['tmp_name'], $destino)) {
            // Usando FFmpeg para obter a duração do vídeo
            $output = [];
            $returnVar = 0;
            exec("ffmpeg -i $destino 2>&1", $output, $returnVar);

            // Inicializa a variável de duração
            $duration = '';
            foreach ($output as $line) {
                // Procura pela duração no output
                if (preg_match('/Duration: (\d{2}:\d{2}:\d{2})/', $line, $matches)) {
                    $duration = $matches[1]; // Duração no formato HH:MM:SS
                    break;
                }
            }

            // Agora que o upload foi bem-sucedido, podemos salvar os detalhes no banco de dados
            // Prepara a query para inserir os dados do vídeo no banco de dados
            $stmt = $conn->prepare("INSERT INTO videos (id_usuario, titulo, descricao, caminho_arquivo, duracao, data_upload) VALUES (?, ?, ?, ?, ?, NOW())");

            // Se a preparação da query for bem-sucedida
            if ($stmt) {
                // Associa os valores aos parâmetros, incluindo a duração
                $stmt->bind_param('issss', $userId, $title, $description, $videoName, $duration);

                // Executa a query
                if ($stmt->execute()) {
                    echo 'Vídeo enviado com sucesso!';
                } else {
                    echo 'Erro ao salvar os dados do vídeo no banco de dados: ' . $stmt->error;
                }
            } else {
                echo 'Erro ao preparar a query: ' . $conn->error;
            }
        } else {
            echo 'Erro ao mover o vídeo para o diretório de upload.';
        }
    } else {
        echo 'Erro no upload do vídeo: ' . $video['error'];
    }
}


function homeVideos($conn) {
    // Busca todos os vídeos do banco de dados, incluindo a duração
    $query = "SELECT v.id, v.titulo, v.descricao, v.caminho_arquivo, v.visualizacoes, v.duracao, v.data_upload, u.nome AS canal
              FROM videos v
              JOIN usuarios u ON v.id_usuario = u.id
              ORDER BY v.data_upload DESC";

    $result = $conn->query($query);

    // Verifica se há vídeos disponíveis
    $videos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $videos[] = $row;
        }
    }

    return $videos;
}

function video($conn, $id) {
    $stmt = $conn->prepare("SELECT id_usuario, titulo, descricao, caminho_arquivo, data_upload, visualizacoes FROM videos WHERE id = ?");

    if($stmt) {
        $stmt->bind_param('s', $id);
        $stmt->execute();

        $stmt->bind_result($user_id, $title, $description, $file, $data_upload, $views);
        $stmt->fetch();

        $stmt->close();

        $views++;

        $update_stmt = $conn->prepare("UPDATE videos SET visualizacoes = ? WHERE id = ?");
        if ($update_stmt) {
            $update_stmt->bind_param('is', $views, $id); // 'i' para o número inteiro de views, 's' para o id (string)
            $update_stmt->execute();
            $update_stmt->close(); // Fecha o statement de atualização
        }
        

        return [
            'id' => $id,
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'file' => $file,
            'data_upload' => $data_upload,
            'views' => $views
        ];
    }
    return null;
}

function inserirComentario($conn, $video_id, $usuario, $comentario) {
    $stmt = $conn->prepare("INSERT INTO comentarios (video_id, usuario, comentario) VALUES (?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param('iss', $video_id, $usuario, $comentario);
        if ($stmt->execute()) {
            return true;
        } else {
            return "Erro ao inserir comentário: " . $stmt->error;
        }
        $stmt->close();
    } else {
        return "Erro ao preparar a query: " . $conn->error;
    }
}

function carregarComentarios($conn, $video_id) {
    $stmt = $conn->prepare("SELECT usuario, comentario, data FROM comentarios WHERE video_id = ? ORDER BY data DESC");
    if ($stmt) {
        $stmt->bind_param('i', $video_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $comentarios = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $comentarios[] = $row;
            }
        }
        $stmt->close();
        return $comentarios;
    } else {
        return "Erro ao preparar a query: " . $conn->error;
    }
}

?>