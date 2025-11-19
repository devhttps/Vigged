<?php
/**
 * Processamento de Atualização de Perfil PCD
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_PCD);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil-pcd.php');
    exit;
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$currentUser = getCurrentUser();
$user_id = $currentUser['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['perfil_errors'] = ['Usuário não autenticado.'];
    header('Location: perfil-pcd.php');
    exit;
}

// Coletar dados
$nome = sanitizeInput($_POST['nome'] ?? '');
$cpf = sanitizeInput($_POST['cpf'] ?? '');
$telefone = sanitizeInput($_POST['telefone'] ?? '');
$data_nascimento = sanitizeInput($_POST['data_nascimento'] ?? '');
$tipo_deficiencia = sanitizeInput($_POST['tipo_deficiencia'] ?? '');
$especifique_outra = sanitizeInput($_POST['especifique_outra'] ?? '');
$cid = sanitizeInput($_POST['cid'] ?? '');
$possui_laudo = isset($_POST['possui_laudo']) && $_POST['possui_laudo'] === 'sim';
$recursos_acessibilidade = isset($_POST['recursos']) ? $_POST['recursos'] : [];
$outras_necessidades = sanitizeInput($_POST['outras_necessidades'] ?? '');

// Validações
$errors = [];

if (empty($nome)) {
    $errors[] = "Nome completo é obrigatório.";
}

if (!empty($cpf) && strlen(preg_replace('/[^0-9]/', '', $cpf)) !== 11) {
    $errors[] = "CPF inválido.";
}

if (empty($tipo_deficiencia)) {
    $errors[] = "Tipo de deficiência é obrigatório.";
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
        } else {
            $errors[] = "Erro ao fazer upload do arquivo.";
        }
    }
}

if (!empty($errors)) {
    $_SESSION['perfil_errors'] = $errors;
    header('Location: perfil-pcd.php');
    exit;
}

// Converter dados
$cpf_cleaned = !empty($cpf) ? preg_replace('/[^0-9]/', '', $cpf) : null;
$data_nascimento_formatted = null;
if (!empty($data_nascimento)) {
    $date_parts = explode('/', $data_nascimento);
    if (count($date_parts) === 3) {
        $data_nascimento_formatted = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
    }
}
$recursos_json = !empty($recursos_acessibilidade) ? json_encode($recursos_acessibilidade) : null;

// Atualizar no banco
$pdo = getDBConnection();
if (!$pdo) {
    $_SESSION['perfil_errors'] = ['Erro de conexão com banco de dados.'];
    header('Location: perfil-pcd.php');
    exit;
}

try {
    // Se há novo laudo, atualizar o path
    $updateLaudo = '';
    $params = [
        ':nome' => $nome,
        ':cpf' => $cpf_cleaned,
        ':telefone' => !empty($telefone) ? $telefone : null,
        ':data_nascimento' => $data_nascimento_formatted,
        ':tipo_deficiencia' => $tipo_deficiencia,
        ':especifique_outra' => !empty($especifique_outra) ? $especifique_outra : null,
        ':cid' => !empty($cid) ? $cid : null,
        ':possui_laudo' => $possui_laudo,
        ':recursos_acessibilidade' => $recursos_json,
        ':outras_necessidades' => !empty($outras_necessidades) ? $outras_necessidades : null,
        ':id' => $user_id
    ];
    
    if ($laudo_medico_path) {
        $updateLaudo = ', laudo_medico_path = :laudo_medico_path';
        $params[':laudo_medico_path'] = $laudo_medico_path;
    }
    
    $stmt = $pdo->prepare("
        UPDATE users SET
            nome = :nome,
            cpf = :cpf,
            telefone = :telefone,
            data_nascimento = :data_nascimento,
            tipo_deficiencia = :tipo_deficiencia,
            especifique_outra = :especifique_outra,
            cid = :cid,
            possui_laudo = :possui_laudo,
            recursos_acessibilidade = :recursos_acessibilidade,
            outras_necessidades = :outras_necessidades
            $updateLaudo
        WHERE id = :id AND tipo = 'pcd'
    ");
    
    $stmt->execute($params);
    
    $_SESSION['perfil_success'] = 'Perfil atualizado com sucesso!';
    header('Location: perfil-pcd.php');
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao atualizar perfil PCD: " . $e->getMessage());
    $_SESSION['perfil_errors'] = ['Erro ao atualizar perfil. Tente novamente.'];
    header('Location: perfil-pcd.php');
    exit;
}

