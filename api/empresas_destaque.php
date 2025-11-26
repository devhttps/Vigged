<?php
/**
 * API para listar empresas em destaque (que mais postam vagas)
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

startSecureSession();

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro de conexão']);
        exit;
    }
    
    $limit = isset($_GET['limit']) ? min(10, max(1, (int)$_GET['limit'])) : 6;
    
    // Buscar empresas com mais vagas ativas
    $query = "
        SELECT 
            c.id,
            c.nome_fantasia,
            c.razao_social,
            c.logo_path,
            c.setor,
            c.cidade,
            c.estado,
            COUNT(DISTINCT j.id) as total_vagas,
            COUNT(DISTINCT CASE WHEN j.status = 'ativa' THEN j.id END) as vagas_ativas,
            COUNT(DISTINCT a.id) as total_candidaturas
        FROM companies c
        INNER JOIN jobs j ON c.id = j.company_id
        LEFT JOIN applications a ON j.id = a.job_id
        WHERE c.status = 'ativa'
        GROUP BY c.id, c.nome_fantasia, c.razao_social, c.logo_path, c.setor, c.cidade, c.estado
        HAVING vagas_ativas > 0
        ORDER BY vagas_ativas DESC, total_vagas DESC
        LIMIT :limit
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatar dados para resposta
    $empresasFormatadas = array_map(function($empresa) {
        return [
            'id' => (int)$empresa['id'],
            'nome' => $empresa['nome_fantasia'] ?: $empresa['razao_social'],
            'razao_social' => $empresa['razao_social'],
            'logo' => $empresa['logo_path'] ? (strpos($empresa['logo_path'], 'http') === 0 ? $empresa['logo_path'] : BASE_URL . '/' . $empresa['logo_path']) : null,
            'setor' => $empresa['setor'] ?: 'Não especificado',
            'localizacao' => trim(($empresa['cidade'] ?: '') . ', ' . ($empresa['estado'] ?: ''), ', '),
            'total_vagas' => (int)$empresa['total_vagas'],
            'vagas_ativas' => (int)$empresa['vagas_ativas'],
            'total_candidaturas' => (int)$empresa['total_candidaturas']
        ];
    }, $empresas);
    
    echo json_encode([
        'success' => true,
        'data' => $empresasFormatadas
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar empresas em destaque: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao buscar empresas']);
    exit;
}
?>

