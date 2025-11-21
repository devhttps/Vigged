<?php
/**
 * API para Obter Detalhes de uma Vaga
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';

header('Content-Type: application/json');

$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($job_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da vaga inválido']);
    exit;
}

$pdo = getDBConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão']);
    exit;
}

try {
    // Buscar vaga com dados da empresa
    $stmt = $pdo->prepare("
        SELECT 
            j.*,
            c.id as company_id,
            c.nome_fantasia as empresa_nome,
            c.razao_social as empresa_razao_social,
            c.logo_path as empresa_logo,
            c.descricao as empresa_descricao,
            c.setor as empresa_setor,
            c.cidade as empresa_cidade,
            c.estado as empresa_estado,
            COUNT(DISTINCT a.id) as total_candidaturas
        FROM jobs j
        INNER JOIN companies c ON j.company_id = c.id
        LEFT JOIN applications a ON j.id = a.job_id
        WHERE j.id = ? AND j.status = 'ativa' AND c.status IN ('ativa', 'pendente')
        GROUP BY j.id
    ");
    
    $stmt->execute([$job_id]);
    $vaga = $stmt->fetch();
    
    if (!$vaga) {
        http_response_code(404);
        echo json_encode(['error' => 'Vaga não encontrada']);
        exit;
    }
    
    // Incrementar visualizações
    $stmt = $pdo->prepare("UPDATE jobs SET visualizacoes = visualizacoes + 1 WHERE id = ?");
    $stmt->execute([$job_id]);
    
    echo json_encode([
        'success' => true,
        'data' => $vaga
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar detalhes da vaga: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar vaga']);
    exit;
}

