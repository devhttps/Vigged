<?php
/**
 * Processamento de Atualização de Perfil Empresa
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_COMPANY);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil-empresa.php');
    exit;
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$currentUser = getCurrentUser();
$company_id = $currentUser['id'] ?? null;

if (!$company_id) {
    $_SESSION['perfil_errors'] = ['Empresa não autenticada.'];
    header('Location: perfil-empresa.php');
    exit;
}

// Coletar dados
$nome_fantasia = sanitizeInput($_POST['nome_fantasia'] ?? '');
$setor = sanitizeInput($_POST['setor'] ?? '');
$website = sanitizeInput($_POST['website'] ?? '');
$descricao = sanitizeInput($_POST['descricao'] ?? '');
$cep = sanitizeInput($_POST['cep'] ?? '');
$estado = sanitizeInput($_POST['estado'] ?? '');
$cidade = sanitizeInput($_POST['cidade'] ?? '');
$bairro = sanitizeInput($_POST['bairro'] ?? '');
$logradouro = sanitizeInput($_POST['logradouro'] ?? '');
$numero = sanitizeInput($_POST['numero'] ?? '');
$complemento = sanitizeInput($_POST['complemento'] ?? '');
$telefone_empresa = sanitizeInput($_POST['telefone_empresa'] ?? '');
$nome_responsavel = sanitizeInput($_POST['nome_responsavel'] ?? '');
$cargo_responsavel = sanitizeInput($_POST['cargo_responsavel'] ?? '');
$email_responsavel = sanitizeInput($_POST['email_responsavel'] ?? '');
$telefone_responsavel = sanitizeInput($_POST['telefone_responsavel'] ?? '');
$ja_contrata_pcd = isset($_POST['ja_contrata_pcd']) && $_POST['ja_contrata_pcd'] === 'sim';
$recursos_acessibilidade = isset($_POST['recursos_acessibilidade']) ? $_POST['recursos_acessibilidade'] : [];
$politica_inclusao = sanitizeInput($_POST['politica_inclusao'] ?? '');

// Validações
$errors = [];

if (empty($nome_fantasia)) {
    $errors[] = "Nome fantasia é obrigatório.";
}

if (!empty($email_responsavel) && !filter_var($email_responsavel, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email do responsável inválido.";
}

// Processar upload de logo
$logo_path = null;
if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['logo_empresa'];
    
    if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
        $errors[] = "Tipo de arquivo inválido para logo. Apenas JPG e PNG são permitidos.";
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $errors[] = "Logo muito grande. Tamanho máximo: 5MB.";
    }
    
    if (empty($errors)) {
        $upload_dir = UPLOADS_PATH . '/logos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('logo_') . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $logo_path = 'uploads/logos/' . $file_name;
        } else {
            $errors[] = "Erro ao fazer upload da logo.";
        }
    }
}

if (!empty($errors)) {
    $_SESSION['perfil_errors'] = $errors;
    header('Location: perfil-empresa.php');
    exit;
}

// Converter dados
$recursos_json = !empty($recursos_acessibilidade) ? json_encode($recursos_acessibilidade) : null;

// Atualizar no banco
$pdo = getDBConnection();
if (!$pdo) {
    $_SESSION['perfil_errors'] = ['Erro de conexão com banco de dados.'];
    header('Location: perfil-empresa.php');
    exit;
}

try {
    $updateLogo = '';
    $params = [
        ':nome_fantasia' => $nome_fantasia,
        ':setor' => $setor,
        ':website' => !empty($website) ? $website : null,
        ':descricao' => $descricao,
        ':cep' => !empty($cep) ? $cep : null,
        ':estado' => !empty($estado) ? $estado : null,
        ':cidade' => !empty($cidade) ? $cidade : null,
        ':bairro' => !empty($bairro) ? $bairro : null,
        ':logradouro' => !empty($logradouro) ? $logradouro : null,
        ':numero' => !empty($numero) ? $numero : null,
        ':complemento' => !empty($complemento) ? $complemento : null,
        ':telefone_empresa' => !empty($telefone_empresa) ? $telefone_empresa : null,
        ':nome_responsavel' => $nome_responsavel,
        ':cargo_responsavel' => $cargo_responsavel,
        ':email_responsavel' => $email_responsavel,
        ':telefone_responsavel' => !empty($telefone_responsavel) ? $telefone_responsavel : null,
        ':ja_contrata_pcd' => $ja_contrata_pcd,
        ':recursos_acessibilidade' => $recursos_json,
        ':politica_inclusao' => !empty($politica_inclusao) ? $politica_inclusao : null,
        ':id' => $company_id
    ];
    
    if ($logo_path) {
        $updateLogo = ', logo_path = :logo_path';
        $params[':logo_path'] = $logo_path;
    }
    
    $stmt = $pdo->prepare("
        UPDATE companies SET
            nome_fantasia = :nome_fantasia,
            setor = :setor,
            website = :website,
            descricao = :descricao,
            cep = :cep,
            estado = :estado,
            cidade = :cidade,
            bairro = :bairro,
            logradouro = :logradouro,
            numero = :numero,
            complemento = :complemento,
            telefone_empresa = :telefone_empresa,
            nome_responsavel = :nome_responsavel,
            cargo_responsavel = :cargo_responsavel,
            email_responsavel = :email_responsavel,
            telefone_responsavel = :telefone_responsavel,
            ja_contrata_pcd = :ja_contrata_pcd,
            recursos_acessibilidade = :recursos_acessibilidade,
            politica_inclusao = :politica_inclusao
            $updateLogo
        WHERE id = :id
    ");
    
    $stmt->execute($params);
    
    $_SESSION['perfil_success'] = 'Perfil atualizado com sucesso!';
    header('Location: perfil-empresa.php');
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao atualizar perfil empresa: " . $e->getMessage());
    $_SESSION['perfil_errors'] = ['Erro ao atualizar perfil. Tente novamente.'];
    header('Location: perfil-empresa.php');
    exit;
}

