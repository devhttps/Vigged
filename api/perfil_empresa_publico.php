<?php
/**
 * API para obter perfil público de uma empresa
 * Vigged - Plataforma de Inclusão e Oportunidades
 * Acesso público (não requer autenticação)
 */

require_once '../config/constants.php';
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$company_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$company_id || $company_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID da empresa inválido']);
    exit;
}

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro de conexão']);
        exit;
    }
    
    // Buscar dados públicos da empresa
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.nome_fantasia,
            c.razao_social,
            c.logo_path,
            c.setor,
            c.website,
            c.descricao,
            c.cidade,
            c.estado,
            c.bairro,
            c.logradouro,
            c.numero,
            c.complemento,
            c.cep,
            c.ja_contrata_pcd,
            c.politica_inclusao,
            COUNT(DISTINCT j.id) as total_vagas,
            COUNT(DISTINCT CASE WHEN j.status = 'ativa' THEN j.id END) as vagas_ativas,
            COUNT(DISTINCT a.id) as total_candidaturas
        FROM companies c
        LEFT JOIN jobs j ON c.id = j.company_id
        LEFT JOIN applications a ON j.id = a.job_id
        WHERE c.id = :company_id AND c.status = 'ativa'
        GROUP BY c.id
    ");
    
    $stmt->execute([':company_id' => $company_id]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$empresa) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Empresa não encontrada ou inativa']);
        exit;
    }
    
    // Formatar endereço completo
    $endereco = [];
    if (!empty($empresa['logradouro'])) {
        $endereco[] = $empresa['logradouro'];
        if (!empty($empresa['numero'])) $endereco[] = $empresa['numero'];
    }
    if (!empty($empresa['bairro'])) $endereco[] = $empresa['bairro'];
    if (!empty($empresa['cidade'])) $endereco[] = $empresa['cidade'];
    if (!empty($empresa['estado'])) $endereco[] = $empresa['estado'];
    if (!empty($empresa['cep'])) $endereco[] = 'CEP: ' . $empresa['cep'];
    
    // Formatar logo
    $logoUrl = null;
    if (!empty($empresa['logo_path'])) {
        $logoUrl = strpos($empresa['logo_path'], 'http') === 0 
            ? $empresa['logo_path'] 
            : BASE_URL . '/' . $empresa['logo_path'];
    }
    
    // Buscar vagas ativas da empresa
    $vagasStmt = $pdo->prepare("
        SELECT 
            j.id,
            j.titulo,
            j.localizacao,
            j.tipo_contrato,
            j.faixa_salarial,
            j.created_at
        FROM jobs j
        WHERE j.company_id = :company_id AND j.status = 'ativa'
        ORDER BY j.created_at DESC
        LIMIT 5
    ");
    
    $vagasStmt->execute([':company_id' => $company_id]);
    $vagas = $vagasStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => (int)$empresa['id'],
            'nome_fantasia' => $empresa['nome_fantasia'],
            'razao_social' => $empresa['razao_social'],
            'nome' => $empresa['nome_fantasia'] ?: $empresa['razao_social'],
            'logo' => $logoUrl,
            'setor' => $empresa['setor'] ?: 'Não especificado',
            'website' => $empresa['website'],
            'descricao' => $empresa['descricao'],
            'endereco_completo' => implode(', ', $endereco),
            'cidade' => $empresa['cidade'],
            'estado' => $empresa['estado'],
            'ja_contrata_pcd' => (bool)$empresa['ja_contrata_pcd'],
            'politica_inclusao' => $empresa['politica_inclusao'],
            'total_vagas' => (int)$empresa['total_vagas'],
            'vagas_ativas' => (int)$empresa['vagas_ativas'],
            'total_candidaturas' => (int)$empresa['total_candidaturas'],
            'vagas_recentes' => $vagas
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar perfil público da empresa: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao buscar dados da empresa']);
    exit;
}
?>

