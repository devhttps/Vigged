<?php
/**
 * Teste de Instalação
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * Acesse: http://localhost/vigged/install/test.php
 * Para testar se a instalação foi bem-sucedida
 */

header('Content-Type: application/json');

$tests = [];
$allPassed = true;

// Teste 1: Verificar arquivo de configuração
$tests[] = [
    'name' => 'Arquivo database.php',
    'status' => file_exists('../config/database.php') ? 'ok' : 'error',
    'message' => file_exists('../config/database.php') ? 'Existe' : 'Não encontrado'
];
if (!file_exists('../config/database.php')) $allPassed = false;

// Teste 2: Verificar conexão com banco
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
    $connectionOk = testDBConnection();
    $tests[] = [
        'name' => 'Conexão com Banco de Dados',
        'status' => $connectionOk ? 'ok' : 'error',
        'message' => $connectionOk ? 'Conectado' : 'Erro na conexão'
    ];
    if (!$connectionOk) $allPassed = false;
    
    // Teste 3: Verificar tabelas
    if ($connectionOk) {
        $pdo = getDBConnection();
        $tables = ['users', 'companies', 'jobs', 'applications'];
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                $exists = $stmt->rowCount() > 0;
                $tests[] = [
                    'name' => "Tabela $table",
                    'status' => $exists ? 'ok' : 'error',
                    'message' => $exists ? 'Existe' : 'Não encontrada'
                ];
                if (!$exists) $allPassed = false;
            } catch (PDOException $e) {
                $tests[] = [
                    'name' => "Tabela $table",
                    'status' => 'error',
                    'message' => 'Erro: ' . $e->getMessage()
                ];
                $allPassed = false;
            }
        }
    }
}

// Teste 4: Verificar diretórios de upload
$uploadDirs = ['laudos', 'documentos', 'logos', 'curriculos'];
foreach ($uploadDirs as $dir) {
    $path = "../uploads/$dir";
    $exists = file_exists($path);
    $writable = $exists && is_writable($path);
    $tests[] = [
        'name' => "Diretório uploads/$dir",
        'status' => $writable ? 'ok' : ($exists ? 'warning' : 'error'),
        'message' => $writable ? 'Gravável' : ($exists ? 'Sem permissão' : 'Não existe')
    ];
    if (!$writable) $allPassed = false;
}

// Teste 5: Verificar arquivo .installed
$tests[] = [
    'name' => 'Arquivo .installed',
    'status' => file_exists('../.installed') ? 'ok' : 'warning',
    'message' => file_exists('../.installed') ? 'Existe' : 'Não encontrado (pode ser normal)'
];

echo json_encode([
    'success' => $allPassed,
    'tests' => $tests,
    'summary' => [
        'total' => count($tests),
        'passed' => count(array_filter($tests, fn($t) => $t['status'] === 'ok')),
        'warnings' => count(array_filter($tests, fn($t) => $t['status'] === 'warning')),
        'errors' => count(array_filter($tests, fn($t) => $t['status'] === 'error'))
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

