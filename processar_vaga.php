<?php
/**
 * Processamento de Cadastro/Edição de Vaga
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'config/auth.php';

// Iniciar sessão
startSecureSession();

// Verificar autenticação e tipo de usuário
requireAuth(USER_TYPE_COMPANY);

// Verificar se é requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil-empresa.php');
    exit;
}

// Função para sanitizar dados
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Coletar dados do formulário
$titulo = sanitizeInput($_POST['titulo'] ?? '');
$descricao = sanitizeInput($_POST['descricao'] ?? '');
$requisitos = sanitizeInput($_POST['requisitos'] ?? '');
$localizacao = sanitizeInput($_POST['localizacao'] ?? '');
$tipo_contrato = sanitizeInput($_POST['tipo_contrato'] ?? '');
$faixa_salarial = sanitizeInput($_POST['faixa_salarial'] ?? '');
$destacada = isset($_POST['destacada']) && $_POST['destacada'] === 'sim';
$action = sanitizeInput($_POST['action'] ?? 'create'); // create ou update
$job_id = isset($_POST['job_id']) ? (int)$_POST['job_id'] : null;

// Validações
$errors = [];

if (empty($titulo)) {
    $errors[] = "Título da vaga é obrigatório.";
}

if (empty($descricao)) {
    $errors[] = "Descrição da vaga é obrigatória.";
}

if (empty($localizacao)) {
    $errors[] = "Localização é obrigatória.";
}

if (empty($tipo_contrato)) {
    $errors[] = "Tipo de contrato é obrigatório.";
}

if (!in_array($tipo_contrato, ['CLT', 'PJ', 'Estagio', 'Temporario'])) {
    $errors[] = "Tipo de contrato inválido.";
}

// Obter ID da empresa logada
$currentUser = getCurrentUser();
if (!$currentUser || !isset($currentUser['user_id'])) {
    $errors[] = "Usuário não autenticado.";
}

$company_id = $currentUser['user_id'] ?? null;

// Se houver erros, retornar JSON com erros
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Conectar ao banco
$pdo = getDBConnection();
if (!$pdo) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'errors' => ['Erro de conexão com banco de dados.']]);
    exit;
}

try {
    if ($action === 'update' && $job_id) {
        // Verificar se a vaga pertence à empresa
        $stmt = $pdo->prepare("SELECT company_id FROM jobs WHERE id = ? AND company_id = ?");
        $stmt->execute([$job_id, $company_id]);
        $job = $stmt->fetch();
        
        if (!$job) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => ['Vaga não encontrada ou sem permissão.']]);
            exit;
        }
        
        // Atualizar vaga
        $stmt = $pdo->prepare("
            UPDATE jobs SET
                titulo = :titulo,
                descricao = :descricao,
                requisitos = :requisitos,
                localizacao = :localizacao,
                tipo_contrato = :tipo_contrato,
                faixa_salarial = :faixa_salarial,
                destacada = :destacada,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id AND company_id = :company_id
        ");
        
        $stmt->execute([
            ':titulo' => $titulo,
            ':descricao' => $descricao,
            ':requisitos' => !empty($requisitos) ? $requisitos : null,
            ':localizacao' => $localizacao,
            ':tipo_contrato' => $tipo_contrato,
            ':faixa_salarial' => !empty($faixa_salarial) ? $faixa_salarial : null,
            ':destacada' => $destacada ? 1 : 0,
            ':id' => $job_id,
            ':company_id' => $company_id
        ]);
        
        $message = 'Vaga atualizada com sucesso!';
    } else {
        // Criar nova vaga
        $stmt = $pdo->prepare("
            INSERT INTO jobs (
                company_id, titulo, descricao, requisitos, localizacao,
                tipo_contrato, faixa_salarial, destacada, status
            ) VALUES (
                :company_id, :titulo, :descricao, :requisitos, :localizacao,
                :tipo_contrato, :faixa_salarial, :destacada, 'ativa'
            )
        ");
        
        $stmt->execute([
            ':company_id' => $company_id,
            ':titulo' => $titulo,
            ':descricao' => $descricao,
            ':requisitos' => !empty($requisitos) ? $requisitos : null,
            ':localizacao' => $localizacao,
            ':tipo_contrato' => $tipo_contrato,
            ':faixa_salarial' => !empty($faixa_salarial) ? $faixa_salarial : null,
            ':destacada' => $destacada ? 1 : 0
        ]);
        
        $job_id = $pdo->lastInsertId();
        $message = 'Vaga publicada com sucesso!';
    }
    
    // Retornar sucesso
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => $message,
        'job_id' => $job_id
    ]);
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao processar vaga: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'errors' => ['Erro ao processar vaga. Tente novamente.']]);
    exit;
}

