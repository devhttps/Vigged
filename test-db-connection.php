<?php
/**
 * Script de Teste de Conexão com Banco de Dados
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * Este script testa a conexão com o banco de dados e verifica se todas as tabelas existem.
 * Acesse via: http://localhost/vigged/test-db-connection.php
 */

require_once 'config/database.php';
require_once 'config/constants.php';

// Desabilitar exibição de erros em produção
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Conexão - Vigged</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-purple-600 mb-8">Teste de Conexão com Banco de Dados</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-6 space-y-6">
            <?php
            // Teste 1: Conexão básica
            echo '<div class="border-b pb-4">';
            echo '<h2 class="text-xl font-semibold mb-2">1. Teste de Conexão</h2>';
            
            $pdo = getDBConnection();
            if ($pdo) {
                echo '<p class="text-green-600">✅ Conexão estabelecida com sucesso!</p>';
                echo '<p class="text-sm text-gray-600 mt-2">Host: ' . DB_HOST . ' | Database: ' . DB_NAME . '</p>';
            } else {
                echo '<p class="text-red-600">❌ Falha na conexão com o banco de dados.</p>';
                echo '<p class="text-sm text-gray-600 mt-2">Verifique as configurações em config/database.php</p>';
                echo '</div></div></body></html>';
                exit;
            }
            echo '</div>';
            
            // Teste 2: Verificar tabelas
            echo '<div class="border-b pb-4">';
            echo '<h2 class="text-xl font-semibold mb-2">2. Verificação de Tabelas</h2>';
            
            $requiredTables = ['users', 'companies', 'jobs', 'applications', 'admin_logs'];
            $existingTables = [];
            
            try {
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                foreach ($requiredTables as $table) {
                    if (in_array($table, $tables)) {
                        echo '<p class="text-green-600">✅ Tabela <strong>' . $table . '</strong> existe</p>';
                        $existingTables[] = $table;
                    } else {
                        echo '<p class="text-red-600">❌ Tabela <strong>' . $table . '</strong> não encontrada</p>';
                    }
                }
            } catch (PDOException $e) {
                echo '<p class="text-red-600">❌ Erro ao verificar tabelas: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            echo '</div>';
            
            // Teste 3: Verificar usuário admin padrão
            echo '<div class="border-b pb-4">';
            echo '<h2 class="text-xl font-semibold mb-2">3. Verificação de Usuário Admin</h2>';
            
            try {
                $stmt = $pdo->prepare("SELECT id, nome, email, tipo, status FROM users WHERE tipo = 'admin' LIMIT 1");
                $stmt->execute();
                $admin = $stmt->fetch();
                
                if ($admin) {
                    echo '<p class="text-green-600">✅ Usuário administrador encontrado:</p>';
                    echo '<ul class="list-disc list-inside ml-4 mt-2 text-sm text-gray-700">';
                    echo '<li>ID: ' . htmlspecialchars($admin['id']) . '</li>';
                    echo '<li>Nome: ' . htmlspecialchars($admin['nome']) . '</li>';
                    echo '<li>Email: ' . htmlspecialchars($admin['email']) . '</li>';
                    echo '<li>Status: ' . htmlspecialchars($admin['status']) . '</li>';
                    echo '</ul>';
                } else {
                    echo '<p class="text-yellow-600">⚠️ Nenhum usuário administrador encontrado.</p>';
                    echo '<p class="text-sm text-gray-600 mt-2">Execute o script config/database.sql para criar o admin padrão.</p>';
                }
            } catch (PDOException $e) {
                echo '<p class="text-red-600">❌ Erro ao verificar admin: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            echo '</div>';
            
            // Teste 4: Contar registros
            echo '<div class="pb-4">';
            echo '<h2 class="text-xl font-semibold mb-2">4. Estatísticas do Banco</h2>';
            
            try {
                $stats = [];
                
                foreach ($existingTables as $table) {
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM `$table`");
                    $result = $stmt->fetch();
                    $stats[$table] = $result['total'];
                }
                
                echo '<div class="grid grid-cols-2 gap-4 mt-2">';
                foreach ($stats as $table => $count) {
                    echo '<div class="bg-gray-50 p-3 rounded">';
                    echo '<p class="font-semibold text-gray-700">' . ucfirst($table) . '</p>';
                    echo '<p class="text-2xl font-bold text-purple-600">' . $count . '</p>';
                    echo '</div>';
                }
                echo '</div>';
            } catch (PDOException $e) {
                echo '<p class="text-red-600">❌ Erro ao contar registros: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            echo '</div>';
            
            // Resumo final
            echo '<div class="mt-6 p-4 bg-purple-50 rounded-lg">';
            echo '<h2 class="text-xl font-semibold mb-2">Resumo</h2>';
            
            $allTablesExist = count($existingTables) === count($requiredTables);
            
            if ($allTablesExist) {
                echo '<p class="text-green-600 font-semibold">✅ Banco de dados configurado corretamente!</p>';
                echo '<p class="text-sm text-gray-600 mt-2">Todas as tabelas necessárias estão presentes.</p>';
            } else {
                echo '<p class="text-yellow-600 font-semibold">⚠️ Banco de dados parcialmente configurado.</p>';
                echo '<p class="text-sm text-gray-600 mt-2">Execute o script config/database.sql para criar as tabelas faltantes.</p>';
            }
            echo '</div>';
            ?>
        </div>
        
        <div class="mt-6 text-center">
            <a href="index.php" class="text-purple-600 hover:text-purple-700">← Voltar para o início</a>
        </div>
    </div>
</body>
</html>

