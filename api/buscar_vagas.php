<?php
/**
 * API de Busca de Vagas
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * Endpoint: GET /api/buscar_vagas.php
 * Parâmetros:
 *   - q: termo de busca (opcional)
 *   - localizacao: filtro por localização (opcional)
 *   - tipo_contrato: filtro por tipo de contrato (opcional)
 *   - destacada: apenas vagas destacadas (opcional, 1 ou 0)
 *   - page: página (padrão: 1)
 *   - limit: itens por página (padrão: 10)
 */

require_once '../config/constants.php';
require_once '../config/database.php';

header('Content-Type: application/json');

// Parâmetros de busca
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$localizacao = isset($_GET['localizacao']) ? trim($_GET['localizacao']) : '';
$tipo_contrato = isset($_GET['tipo_contrato']) ? trim($_GET['tipo_contrato']) : '';
$destacada = isset($_GET['destacada']) ? (int)$_GET['destacada'] : null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 10;
$offset = ($page - 1) * $limit;

// Conectar ao banco
$pdo = getDBConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão com banco de dados']);
    exit;
}

try {
    // Construir query
    $where = ["j.status = 'ativa'"];
    $params = [];
    
    // Busca textual
    if (!empty($search)) {
        $where[] = "(MATCH(j.titulo, j.descricao, j.requisitos) AGAINST(:search IN BOOLEAN MODE) OR j.titulo LIKE :search_like OR j.descricao LIKE :search_like)";
        $params[':search'] = $search;
        $params[':search_like'] = '%' . $search . '%';
    }
    
    // Filtro por localização
    if (!empty($localizacao)) {
        $where[] = "j.localizacao LIKE :localizacao";
        $params[':localizacao'] = '%' . $localizacao . '%';
    }
    
    // Filtro por tipo de contrato
    if (!empty($tipo_contrato)) {
        $where[] = "j.tipo_contrato = :tipo_contrato";
        $params[':tipo_contrato'] = $tipo_contrato;
    }
    
    // Filtro por destacada
    if ($destacada !== null) {
        $where[] = "j.destacada = :destacada";
        $params[':destacada'] = $destacada;
    }
    
    $whereClause = implode(' AND ', $where);
    
    // Query para contar total
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM jobs j
        INNER JOIN companies c ON j.company_id = c.id
        WHERE $whereClause AND c.status = 'ativa'
    ");
    
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    
    $countStmt->execute();
    $total = $countStmt->fetch()['total'];
    
    // Query para buscar vagas
    $query = "
        SELECT 
            j.id,
            j.titulo,
            j.descricao,
            j.requisitos,
            j.localizacao,
            j.tipo_contrato,
            j.faixa_salarial,
            j.destacada,
            j.visualizacoes,
            j.created_at,
            c.id as company_id,
            c.nome_fantasia as empresa_nome,
            c.logo_path as empresa_logo
        FROM jobs j
        INNER JOIN companies c ON j.company_id = c.id
        WHERE $whereClause AND c.status = 'ativa'
        ORDER BY j.destacada DESC, j.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($query);
    
    // Bind dos parâmetros
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $vagas = $stmt->fetchAll();
    
    // Formatar resposta
    $response = [
        'success' => true,
        'data' => $vagas,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar vagas: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar vagas']);
    exit;
}

