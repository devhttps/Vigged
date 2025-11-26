<?php
/**
 * Processamento de Atualização de Perfil PCD
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'config/auth.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/image-utils.php';

startSecureSession();
requireAuth(USER_TYPE_PCD);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil-pcd.php');
    exit;
}

// Validar token CSRF
requireCSRFToken('perfil-pcd.php');

$currentUser = getCurrentUser();
$user_id = $currentUser['id'] ?? null;

if (!$user_id) {
    $_SESSION['perfil_errors'] = ['Usuário não autenticado.'];
    header('Location: perfil-pcd.php');
    exit;
}

// Coletar dados básicos
$nome = sanitizeInput($_POST['nome'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$telefone = sanitizeInput($_POST['telefone'] ?? '');
$sobre = sanitizeInput($_POST['sobre'] ?? '');

// Endereço
$cep = sanitizeInput($_POST['cep'] ?? '');
$estado = strtoupper(sanitizeInput($_POST['estado'] ?? ''));
$cidade = sanitizeInput($_POST['cidade'] ?? '');
$bairro = sanitizeInput($_POST['bairro'] ?? '');
$logradouro = sanitizeInput($_POST['logradouro'] ?? '');
$numero = sanitizeInput($_POST['numero'] ?? '');
$complemento = sanitizeInput($_POST['complemento'] ?? '');

// Habilidades (array)
$habilidades = isset($_POST['habilidades']) && is_array($_POST['habilidades']) 
    ? array_filter(array_map('sanitizeInput', $_POST['habilidades'])) 
    : [];

// Formação acadêmica (array)
$formacao = [];
if (isset($_POST['formacao']) && is_array($_POST['formacao'])) {
    foreach ($_POST['formacao'] as $form) {
        if (!empty($form['curso']) && !empty($form['instituicao'])) {
            $formacao[] = [
                'curso' => sanitizeInput($form['curso'] ?? ''),
                'instituicao' => sanitizeInput($form['instituicao'] ?? ''),
                'periodo' => sanitizeInput($form['periodo'] ?? ''),
                'status' => sanitizeInput($form['status'] ?? '')
            ];
        }
    }
}

// Experiências (array)
$experiencias = [];
if (isset($_POST['experiencias']) && is_array($_POST['experiencias'])) {
    foreach ($_POST['experiencias'] as $exp) {
        if (!empty($exp['cargo']) && !empty($exp['empresa'])) {
            $experiencias[] = [
                'cargo' => sanitizeInput($exp['cargo'] ?? ''),
                'empresa' => sanitizeInput($exp['empresa'] ?? ''),
                'periodo' => sanitizeInput($exp['periodo'] ?? ''),
                'descricao' => sanitizeInput($exp['descricao'] ?? '')
            ];
        }
    }
}

// Campos opcionais para alteração de senha
$senha_atual = $_POST['senha_atual'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Validações
$errors = [];

if (empty($nome)) {
    $errors[] = "Nome completo é obrigatório.";
}

if (empty($email) || !validateEmail($email)) {
    $errors[] = "Email válido é obrigatório.";
}

// Validação de alteração de senha (se fornecida)
$alterar_senha = false;
if (!empty($nova_senha) || !empty($senha_atual) || !empty($confirmar_senha)) {
    if (empty($senha_atual)) {
        $errors[] = "Senha atual é obrigatória para alterar a senha.";
    } elseif (empty($nova_senha)) {
        $errors[] = "Nova senha é obrigatória.";
    } elseif (strlen($nova_senha) < 6) {
        $errors[] = "A nova senha deve ter no mínimo 6 caracteres.";
    } elseif ($nova_senha !== $confirmar_senha) {
        $errors[] = "As senhas não coincidem.";
    } else {
        $alterar_senha = true;
    }
}

// Processar upload de foto de perfil
$foto_perfil_path = null;
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $uploadResult = processProfilePhotoUpload($_FILES['foto_perfil'], $user_id);
    
    if (!$uploadResult['success']) {
        $errors[] = "Erro ao processar foto de perfil: " . ($uploadResult['error'] ?? 'Erro desconhecido');
    } else {
        $foto_perfil_path = $uploadResult['path'];
        // Remover foto antiga se existir
        if (!empty($currentUser['foto_perfil'])) {
            $oldPhotoPath = strpos($currentUser['foto_perfil'], '/') === 0 
                ? substr($currentUser['foto_perfil'], 1) 
                : $currentUser['foto_perfil'];
            $fullOldPath = __DIR__ . '/' . $oldPhotoPath;
            if (file_exists($fullOldPath)) {
                @unlink($fullOldPath);
            }
        }
    }
} elseif (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Se houve erro no upload mas não foi "nenhum arquivo"
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'O arquivo excede o tamanho máximo permitido pelo servidor.',
        UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o tamanho máximo permitido pelo formulário.',
        UPLOAD_ERR_PARTIAL => 'O arquivo foi enviado parcialmente.',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta uma pasta temporária.',
        UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o arquivo no disco.',
        UPLOAD_ERR_EXTENSION => 'Uma extensão PHP interrompeu o upload do arquivo.'
    ];
    $errorMsg = $errorMessages[$_FILES['foto_perfil']['error']] ?? 'Erro desconhecido no upload.';
    $errors[] = "Erro ao fazer upload da foto de perfil: " . $errorMsg;
}

// Processar upload de currículo
$curriculo_path = null;
if (isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['curriculo'];
    
    if (!in_array($file['type'], ALLOWED_DOC_TYPES)) {
        $errors[] = "Tipo de arquivo inválido. Apenas PDF é permitido para currículo.";
    } elseif ($file['size'] > MAX_DOCUMENTO_SIZE) {
        $errors[] = "Arquivo muito grande. Tamanho máximo: 10MB.";
    } else {
        $upload_dir = UPLOADS_PATH . '/curriculos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = 'curriculo_' . $user_id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $curriculo_path = 'uploads/curriculos/' . $file_name;
            
            // Remover currículo antigo se existir
            if (!empty($currentUser['curriculo_path']) && file_exists($currentUser['curriculo_path'])) {
                @unlink($currentUser['curriculo_path']);
            }
        } else {
            $errors[] = "Erro ao fazer upload do currículo.";
        }
    }
}

// Verificar duplicação de email (se alterado)
if (!empty($email) && $email !== $currentUser['email']) {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $errors[] = "Este email já está em uso por outro usuário.";
            }
        } catch (PDOException $e) {
            error_log("Erro ao verificar email duplicado: " . $e->getMessage());
        }
    }
}

// Validar senha atual se for alterar senha
if ($alterar_senha) {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT senha FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_data = $stmt->fetch();
            
            if (!$user_data || !password_verify($senha_atual, $user_data['senha'])) {
                $errors[] = "Senha atual incorreta.";
                $alterar_senha = false;
            }
        } catch (PDOException $e) {
            error_log("Erro ao validar senha atual: " . $e->getMessage());
            $errors[] = "Erro ao validar senha atual. Tente novamente.";
            $alterar_senha = false;
        }
    }
}

if (!empty($errors)) {
    $_SESSION['perfil_errors'] = $errors;
    $_SESSION['perfil_data'] = $_POST;
    header('Location: perfil-pcd.php');
    exit;
}

// Converter dados para JSON
$habilidades_json = !empty($habilidades) ? json_encode($habilidades, JSON_UNESCAPED_UNICODE) : null;
$formacao_json = !empty($formacao) ? json_encode($formacao, JSON_UNESCAPED_UNICODE) : null;
$experiencias_json = !empty($experiencias) ? json_encode($experiencias, JSON_UNESCAPED_UNICODE) : null;

// Limpar CEP
$cep_cleaned = !empty($cep) ? preg_replace('/[^0-9-]/', '', $cep) : null;

// Atualizar no banco
$pdo = getDBConnection();
if (!$pdo) {
    $_SESSION['perfil_errors'] = ['Erro de conexão com banco de dados.'];
    header('Location: perfil-pcd.php');
    exit;
}

try {
    // Preparar campos para atualização
    $updateFields = [];
    $params = [':id' => $user_id];
    
    // Campos básicos
    $updateFields[] = "nome = :nome";
    $params[':nome'] = $nome;
    
    $updateFields[] = "email = :email";
    $params[':email'] = $email;
    
    if (!empty($telefone)) {
        $updateFields[] = "telefone = :telefone";
        $params[':telefone'] = $telefone;
    }
    
    // Sobre
    if ($sobre !== '') {
        $updateFields[] = "sobre = :sobre";
        $params[':sobre'] = $sobre;
    } else {
        $updateFields[] = "sobre = NULL";
    }
    
    // Habilidades
    if ($habilidades_json !== null) {
        $updateFields[] = "habilidades = :habilidades";
        $params[':habilidades'] = $habilidades_json;
    } else {
        $updateFields[] = "habilidades = NULL";
    }
    
    // Formação acadêmica
    if ($formacao_json !== null) {
        $updateFields[] = "formacao_academica = :formacao_academica";
        $params[':formacao_academica'] = $formacao_json;
    } else {
        $updateFields[] = "formacao_academica = NULL";
    }
    
    // Experiências
    if ($experiencias_json !== null) {
        $updateFields[] = "experiencias = :experiencias";
        $params[':experiencias'] = $experiencias_json;
    } else {
        $updateFields[] = "experiencias = NULL";
    }
    
    // Endereço
    if ($cep_cleaned !== null) {
        $updateFields[] = "cep = :cep";
        $params[':cep'] = $cep_cleaned;
    } else {
        $updateFields[] = "cep = NULL";
    }
    
    if ($estado !== '') {
        $updateFields[] = "estado = :estado";
        $params[':estado'] = $estado;
    } else {
        $updateFields[] = "estado = NULL";
    }
    
    if ($cidade !== '') {
        $updateFields[] = "cidade = :cidade";
        $params[':cidade'] = $cidade;
    } else {
        $updateFields[] = "cidade = NULL";
    }
    
    if ($bairro !== '') {
        $updateFields[] = "bairro = :bairro";
        $params[':bairro'] = $bairro;
    } else {
        $updateFields[] = "bairro = NULL";
    }
    
    if ($logradouro !== '') {
        $updateFields[] = "logradouro = :logradouro";
        $params[':logradouro'] = $logradouro;
    } else {
        $updateFields[] = "logradouro = NULL";
    }
    
    if ($numero !== '') {
        $updateFields[] = "numero = :numero";
        $params[':numero'] = $numero;
    } else {
        $updateFields[] = "numero = NULL";
    }
    
    if ($complemento !== '') {
        $updateFields[] = "complemento = :complemento";
        $params[':complemento'] = $complemento;
    } else {
        $updateFields[] = "complemento = NULL";
    }
    
    // Foto de perfil
    if ($foto_perfil_path) {
        $updateFields[] = "foto_perfil = :foto_perfil";
        $params[':foto_perfil'] = $foto_perfil_path;
    }
    
    // Currículo
    if ($curriculo_path) {
        $updateFields[] = "curriculo_path = :curriculo_path";
        $params[':curriculo_path'] = $curriculo_path;
    }
    
    // Se for alterar senha
    if ($alterar_senha) {
        $updateFields[] = "senha = :senha";
        $params[':senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
    }
    
    // Montar e executar query
    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id AND tipo = 'pcd'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Verificar se houve atualização
    if ($stmt->rowCount() > 0 || $foto_perfil_path || $curriculo_path) {
        $success_message = 'Perfil atualizado com sucesso!';
        if ($alterar_senha) {
            $success_message .= ' Senha alterada com sucesso.';
        }
        if ($foto_perfil_path) {
            $success_message .= ' Foto atualizada.';
        }
        if ($curriculo_path) {
            $success_message .= ' Currículo atualizado.';
        }
        $_SESSION['perfil_success'] = $success_message;
    } else {
        $_SESSION['perfil_errors'] = ['Nenhuma alteração foi realizada.'];
    }
    
    header('Location: perfil-pcd.php');
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao atualizar perfil PCD: " . $e->getMessage());
    $_SESSION['perfil_errors'] = ['Erro ao atualizar perfil. Tente novamente.'];
    header('Location: perfil-pcd.php');
    exit;
}
