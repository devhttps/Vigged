<?php
/**
 * Processamento de Atualização de Perfil PCD
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'config/auth.php';
require_once 'includes/functions.php';

startSecureSession();
requireAuth(USER_TYPE_PCD);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil-pcd.php');
    exit;
}

$currentUser = getCurrentUser();
$user_id = $currentUser['id'] ?? null;

if (!$user_id) {
    $_SESSION['perfil_errors'] = ['Usuário não autenticado.'];
    header('Location: perfil-pcd.php');
    exit;
}

// Coletar dados
$nome = sanitizeInput($_POST['nome'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$cpf = sanitizeInput($_POST['cpf'] ?? '');
$telefone = sanitizeInput($_POST['telefone'] ?? '');
$data_nascimento = sanitizeInput($_POST['data_nascimento'] ?? '');
$tipo_deficiencia = sanitizeInput($_POST['tipo_deficiencia'] ?? '');
$especifique_outra = sanitizeInput($_POST['especifique_outra'] ?? '');
$cid = sanitizeInput($_POST['cid'] ?? '');
$possui_laudo = isset($_POST['possui_laudo']) && $_POST['possui_laudo'] === 'sim';
$recursos_acessibilidade = isset($_POST['recursos']) ? $_POST['recursos'] : [];
$outras_necessidades = sanitizeInput($_POST['outras_necessidades'] ?? '');

// Campos opcionais para alteração de senha
$senha_atual = $_POST['senha_atual'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Validações
$errors = [];

if (empty($nome)) {
    $errors[] = "Nome completo é obrigatório.";
}

if (!empty($email) && !validateEmail($email)) {
    $errors[] = "Email inválido.";
}

if (!empty($cpf) && !validateCPF($cpf)) {
    $errors[] = "CPF inválido.";
}

if (empty($tipo_deficiencia)) {
    $errors[] = "Tipo de deficiência é obrigatório.";
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

// Processar upload de novo laudo
$laudo_medico_path = null;
if ($possui_laudo && isset($_FILES['laudo_medico']) && $_FILES['laudo_medico']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['laudo_medico'];
    
    if (!in_array($file['type'], ALLOWED_DOC_TYPES)) {
        $errors[] = "Tipo de arquivo inválido. Apenas PDF é permitido.";
    }
    
    if ($file['size'] > MAX_LAUDO_SIZE) {
        $errors[] = "Arquivo muito grande. Tamanho máximo: 5MB.";
    }
    
    if (empty($errors)) {
        $upload_dir = UPLOADS_PATH . '/laudos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('laudo_') . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $laudo_medico_path = 'uploads/laudos/' . $file_name;
            
            // Remover laudo antigo se existir
            if (!empty($currentUser['laudo_medico_path'])) {
                $old_file = $currentUser['laudo_medico_path'];
                if (file_exists($old_file) && strpos($old_file, 'uploads/laudos/') === 0) {
                    @unlink($old_file);
                }
            }
        } else {
            $errors[] = "Erro ao fazer upload do arquivo.";
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

// Converter e limpar CPF
$cpf_cleaned = !empty($cpf) ? preg_replace('/[^0-9]/', '', $cpf) : null;

// Verificar duplicação de CPF (se alterado)
if (!empty($cpf_cleaned)) {
    $current_cpf = preg_replace('/[^0-9]/', '', $currentUser['cpf'] ?? '');
    if ($cpf_cleaned !== $current_cpf) {
        $pdo = getDBConnection();
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE cpf = ? AND id != ?");
                $stmt->execute([$cpf_cleaned, $user_id]);
                if ($stmt->fetch()) {
                    $errors[] = "Este CPF já está em uso por outro usuário.";
                }
            } catch (PDOException $e) {
                error_log("Erro ao verificar CPF duplicado: " . $e->getMessage());
            }
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

// Converter dados
$data_nascimento_formatted = null;
if (!empty($data_nascimento)) {
    $date_parts = explode('/', $data_nascimento);
    if (count($date_parts) === 3) {
        $data_nascimento_formatted = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
    }
}
$recursos_json = !empty($recursos_acessibilidade) ? json_encode($recursos_acessibilidade, JSON_UNESCAPED_UNICODE) : null;

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
    
    if (!empty($email)) {
        $updateFields[] = "email = :email";
        $params[':email'] = $email;
    }
    
    if ($cpf_cleaned !== null) {
        $updateFields[] = "cpf = :cpf";
        $params[':cpf'] = $cpf_cleaned;
    }
    
    if (!empty($telefone)) {
        $updateFields[] = "telefone = :telefone";
        $params[':telefone'] = $telefone;
    }
    
    if ($data_nascimento_formatted !== null) {
        $updateFields[] = "data_nascimento = :data_nascimento";
        $params[':data_nascimento'] = $data_nascimento_formatted;
    }
    
    $updateFields[] = "tipo_deficiencia = :tipo_deficiencia";
    $params[':tipo_deficiencia'] = $tipo_deficiencia;
    
    if ($especifique_outra !== '') {
        $updateFields[] = "especifique_outra = :especifique_outra";
        $params[':especifique_outra'] = $especifique_outra;
    } else {
        $updateFields[] = "especifique_outra = NULL";
    }
    
    if (!empty($cid)) {
        $updateFields[] = "cid = :cid";
        $params[':cid'] = $cid;
    } else {
        $updateFields[] = "cid = NULL";
    }
    
    $updateFields[] = "possui_laudo = :possui_laudo";
    $params[':possui_laudo'] = $possui_laudo ? 1 : 0;
    
    if ($recursos_json !== null) {
        $updateFields[] = "recursos_acessibilidade = :recursos_acessibilidade";
        $params[':recursos_acessibilidade'] = $recursos_json;
    } else {
        $updateFields[] = "recursos_acessibilidade = NULL";
    }
    
    if ($outras_necessidades !== '') {
        $updateFields[] = "outras_necessidades = :outras_necessidades";
        $params[':outras_necessidades'] = $outras_necessidades;
    } else {
        $updateFields[] = "outras_necessidades = NULL";
    }
    
    // Se há novo laudo, atualizar o path
    if ($laudo_medico_path) {
        $updateFields[] = "laudo_medico_path = :laudo_medico_path";
        $params[':laudo_medico_path'] = $laudo_medico_path;
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
    if ($stmt->rowCount() > 0) {
        $success_message = 'Perfil atualizado com sucesso!';
        if ($alterar_senha) {
            $success_message .= ' Senha alterada com sucesso.';
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

