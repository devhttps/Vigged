<?php
/**
 * API para upload de logo da empresa
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_COMPANY);

header('Content-Type: application/json');

$currentUser = getCurrentUser();
$company_id = $currentUser['id'] ?? null;

if (!$company_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nenhum arquivo enviado ou erro no upload']);
    exit;
}

$file = $_FILES['logo'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Validar tipo de arquivo
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP']);
    exit;
}

// Validar tamanho
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Arquivo muito grande. Tamanho máximo: 5MB']);
    exit;
}

// Criar diretório de uploads se não existir
$uploadDir = __DIR__ . '/../uploads/logos/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao criar diretório de uploads']);
        exit;
    }
}

// Gerar nome único para o arquivo
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = 'logo_' . $company_id . '_' . time() . '.' . $extension;
$filePath = $uploadDir . $fileName;

// Mover arquivo
if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao salvar arquivo']);
    exit;
}

// Caminho relativo para salvar no banco
$relativePath = 'uploads/logos/' . $fileName;

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        // Deletar arquivo se não conseguir conectar ao banco
        @unlink($filePath);
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro de conexão']);
        exit;
    }
    
    // Buscar logo antigo para deletar
    $oldLogoStmt = $pdo->prepare("SELECT logo_path FROM companies WHERE id = ?");
    $oldLogoStmt->execute([$company_id]);
    $oldLogo = $oldLogoStmt->fetchColumn();
    
    // Atualizar no banco
    $updateStmt = $pdo->prepare("UPDATE companies SET logo_path = ? WHERE id = ?");
    $updateStmt->execute([$relativePath, $company_id]);
    
    // Deletar logo antigo se existir
    if ($oldLogo && file_exists(__DIR__ . '/../' . $oldLogo)) {
        @unlink(__DIR__ . '/../' . $oldLogo);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Logo atualizado com sucesso',
        'data' => [
            'logo_path' => $relativePath,
            'logo_url' => BASE_URL . '/' . $relativePath
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // Deletar arquivo em caso de erro
    @unlink($filePath);
    error_log("Erro ao atualizar logo da empresa: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao atualizar logo']);
    exit;
}
?>

