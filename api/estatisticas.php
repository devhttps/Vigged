<?php
/**
 * API de Estatísticas e Relatórios
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

startSecureSession();

header('Content-Type: application/json');

$pdo = getDBConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão']);
    exit;
}

$type = $_GET['type'] ?? 'general'; // general, company, admin

try {
    if ($type === 'admin') {
        // Estatísticas para admin
        requireAuth(USER_TYPE_ADMIN);
        
        $stats = [];
        
        // Total de usuários PCD
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE tipo = 'pcd'");
        $stats['total_usuarios_pcd'] = $stmt->fetch()['total'];
        
        // Usuários por status
        $stmt = $pdo->query("SELECT status, COUNT(*) as total FROM users WHERE tipo = 'pcd' GROUP BY status");
        $stats['usuarios_por_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Total de empresas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM companies");
        $stats['total_empresas'] = $stmt->fetch()['total'];
        
        // Empresas por status
        $stmt = $pdo->query("SELECT status, COUNT(*) as total FROM companies GROUP BY status");
        $stats['empresas_por_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Total de vagas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs");
        $stats['total_vagas'] = $stmt->fetch()['total'];
        
        // Vagas por status
        $stmt = $pdo->query("SELECT status, COUNT(*) as total FROM jobs GROUP BY status");
        $stats['vagas_por_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Total de candidaturas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM applications");
        $stats['total_candidaturas'] = $stmt->fetch()['total'];
        
        // Candidaturas por status
        $stmt = $pdo->query("SELECT status, COUNT(*) as total FROM applications GROUP BY status");
        $stats['candidaturas_por_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Vagas mais visualizadas (com candidaturas)
        $stmt = $pdo->query("
            SELECT j.id, j.titulo, j.visualizacoes, 
                   COALESCE(c.nome_fantasia, c.razao_social, 'Empresa') as nome_fantasia, 
                   COUNT(a.id) as total_candidatos
            FROM jobs j
            LEFT JOIN companies c ON j.company_id = c.id
            LEFT JOIN applications a ON j.id = a.job_id
            GROUP BY j.id, j.titulo, j.visualizacoes, c.nome_fantasia, c.razao_social
            ORDER BY j.visualizacoes DESC, total_candidatos DESC
            LIMIT 5
        ");
        $stats['vagas_mais_populares'] = $stmt->fetchAll();
        
        // Candidaturas por mês (últimos 6 meses)
        $stmt = $pdo->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total
            FROM applications
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY mes
            ORDER BY mes ASC
        ");
        $stats['candidaturas_por_mes'] = $stmt->fetchAll();
        
        // Cadastros recentes (últimos 5 usuários e empresas)
        $stmt = $pdo->query("
            SELECT 'user' as tipo, id, nome as nome, email, created_at
            FROM users
            WHERE tipo = 'pcd'
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $recentUsers = $stmt->fetchAll();
        
        $stmt = $pdo->query("
            SELECT 'company' as tipo, id, 
                   COALESCE(nome_fantasia, razao_social, 'Empresa') as nome, 
                   email_corporativo as email, created_at
            FROM companies
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $recentCompanies = $stmt->fetchAll();
        
        // Combinar e ordenar por data
        $recentRegistrations = array_merge($recentUsers, $recentCompanies);
        usort($recentRegistrations, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        $stats['cadastros_recentes'] = array_slice($recentRegistrations, 0, 5);
        
        // Crescimento mensal (usuários e empresas)
        $stmt = $pdo->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total
            FROM users
            WHERE tipo = 'pcd' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY mes
            ORDER BY mes ASC
        ");
        $stats['usuarios_por_mes'] = $stmt->fetchAll();
        
        $stmt = $pdo->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total
            FROM companies
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY mes
            ORDER BY mes ASC
        ");
        $stats['empresas_por_mes'] = $stmt->fetchAll();
        
        // Taxa de conversão (candidaturas / vagas)
        $totalVagas = $stats['total_vagas'];
        $totalCandidaturas = $stats['total_candidaturas'];
        $stats['taxa_conversao'] = $totalVagas > 0 ? round(($totalCandidaturas / $totalVagas) * 100, 2) : 0;
        
        // Vagas ativas
        $stats['vagas_ativas'] = $stats['vagas_por_status']['ativa'] ?? 0;
        
        // Candidaturas aprovadas
        $stats['candidaturas_aprovadas'] = $stats['candidaturas_por_status']['aprovada'] ?? 0;
        
        // Taxa de aprovação
        $stats['taxa_aprovacao'] = $totalCandidaturas > 0 ? round(($stats['candidaturas_aprovadas'] / $totalCandidaturas) * 100, 2) : 0;
        
        echo json_encode([
            'success' => true,
            'data' => $stats
        ], JSON_UNESCAPED_UNICODE);
        
    } elseif ($type === 'company') {
        // Estatísticas para empresa
        requireAuth(USER_TYPE_COMPANY);
        
        $currentUser = getCurrentUser();
        $company_id = $currentUser['user_id'] ?? null;
        
        if (!$company_id) {
            http_response_code(401);
            echo json_encode(['error' => 'Não autenticado']);
            exit;
        }
        
        $stats = [];
        
        // Total de vagas
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM jobs WHERE company_id = ?");
        $stmt->execute([$company_id]);
        $stats['total_vagas'] = $stmt->fetch()['total'];
        
        // Vagas por status
        $stmt = $pdo->prepare("SELECT status, COUNT(*) as total FROM jobs WHERE company_id = ? GROUP BY status");
        $stmt->execute([$company_id]);
        $stats['vagas_por_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Total de candidaturas
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM applications a
            INNER JOIN jobs j ON a.job_id = j.id
            WHERE j.company_id = ?
        ");
        $stmt->execute([$company_id]);
        $stats['total_candidaturas'] = $stmt->fetch()['total'];
        
        // Candidaturas por status
        $stmt = $pdo->prepare("
            SELECT a.status, COUNT(*) as total
            FROM applications a
            INNER JOIN jobs j ON a.job_id = j.id
            WHERE j.company_id = ?
            GROUP BY a.status
        ");
        $stmt->execute([$company_id]);
        $stats['candidaturas_por_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Vagas mais visualizadas
        $stmt = $pdo->prepare("
            SELECT titulo, visualizacoes
            FROM jobs
            WHERE company_id = ?
            ORDER BY visualizacoes DESC
            LIMIT 5
        ");
        $stmt->execute([$company_id]);
        $stats['vagas_mais_visualizadas'] = $stmt->fetchAll();
        
        // Candidaturas por mês
        $stmt = $pdo->prepare("
            SELECT DATE_FORMAT(a.created_at, '%Y-%m') as mes, COUNT(*) as total
            FROM applications a
            INNER JOIN jobs j ON a.job_id = j.id
            WHERE j.company_id = ? AND a.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY mes
            ORDER BY mes DESC
        ");
        $stmt->execute([$company_id]);
        $stats['candidaturas_por_mes'] = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $stats
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        // Estatísticas gerais (públicas)
        $stats = [];
        
        // Total de vagas ativas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs WHERE status = 'ativa'");
        $stats['total_vagas_ativas'] = $stmt->fetch()['total'];
        
        // Total de empresas ativas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM companies WHERE status = 'ativa'");
        $stats['total_empresas_ativas'] = $stmt->fetch()['total'];
        
        // Vagas por tipo de contrato
        $stmt = $pdo->query("SELECT tipo_contrato, COUNT(*) as total FROM jobs WHERE status = 'ativa' GROUP BY tipo_contrato");
        $stats['vagas_por_tipo_contrato'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Vagas por localização (top 5)
        $stmt = $pdo->query("
            SELECT localizacao, COUNT(*) as total
            FROM jobs
            WHERE status = 'ativa'
            GROUP BY localizacao
            ORDER BY total DESC
            LIMIT 5
        ");
        $stats['vagas_por_localizacao'] = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $stats
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    error_log("Erro ao buscar estatísticas: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar estatísticas']);
    exit;
}

