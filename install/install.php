<?php
/**
 * Processamento da Instalação
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

// Verificar se já está instalado
if (file_exists('../config/database.php') && file_exists('../.installed')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Sistema já instalado']);
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

header('Content-Type: application/json');

// Coletar dados do formulário
$db_host = trim($_POST['db_host'] ?? 'localhost');
$db_name = trim($_POST['db_name'] ?? 'vigged_db');
$db_user = trim($_POST['db_user'] ?? 'root');
$db_pass = $_POST['db_pass'] ?? '';
$base_url = rtrim(trim($_POST['base_url'] ?? ''), '/');
$admin_email = trim($_POST['admin_email'] ?? 'admin@vigged.com');
$admin_password = $_POST['admin_password'] ?? 'admin123';

$errors = [];

// Validações
if (empty($db_host)) {
    $errors[] = "Host do banco de dados é obrigatório.";
}

if (empty($db_name)) {
    $errors[] = "Nome do banco de dados é obrigatório.";
}

if (empty($db_user)) {
    $errors[] = "Usuário do banco de dados é obrigatório.";
}

if (empty($base_url)) {
    $errors[] = "URL base é obrigatória.";
}

if (empty($admin_email) || !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email do administrador inválido.";
}

if (empty($admin_password) || strlen($admin_password) < 6) {
    $errors[] = "Senha do administrador deve ter pelo menos 6 caracteres.";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Tentar conectar ao banco de dados
try {
    $dsn = "mysql:host=$db_host;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
    exit;
}

// Criar banco de dados se não existir
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db_name`");
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao criar banco de dados: ' . $e->getMessage()]);
    exit;
}

// Ler e executar script SQL
$sqlFile = '../config/database.sql';
if (!file_exists($sqlFile)) {
    echo json_encode(['success' => false, 'error' => 'Arquivo database.sql não encontrado']);
    exit;
}

$sql = file_get_contents($sqlFile);

// Remover comentários e comandos de criação de banco (já criamos)
$sql = preg_replace('/^--.*$/m', '', $sql);
$sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
$sql = preg_replace('/USE.*?;/i', '', $sql);

// Garantir que estamos usando o banco correto
try {
    $pdo->exec("USE `$db_name`");
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao selecionar banco: ' . $e->getMessage()]);
    exit;
}

// Executar SQL em partes
$statements = array_filter(array_map('trim', explode(';', $sql)));
foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement) && strlen($statement) > 5) {
        try {
            $pdo->exec($statement);
        } catch (PDOException $e) {
            // Ignorar erros de "table already exists" e "Duplicate key"
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'already exists') === false && 
                strpos($errorMsg, 'Duplicate') === false &&
                strpos($errorMsg, 'Duplicate entry') === false) {
                // Log do erro mas continua (pode ser erro de constraint já existente)
                error_log("SQL Warning durante instalação: " . $errorMsg . " - Statement: " . substr($statement, 0, 100));
            }
        }
    }
}

// Atualizar ou criar administrador
$adminPasswordHash = password_hash($admin_password, PASSWORD_DEFAULT);
try {
    // Verificar se admin já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND tipo = 'admin'");
    $stmt->execute([$admin_email]);
    $adminExists = $stmt->fetch();
    
    if ($adminExists) {
        // Atualizar senha
        $stmt = $pdo->prepare("UPDATE users SET senha = ? WHERE email = ? AND tipo = 'admin'");
        $stmt->execute([$adminPasswordHash, $admin_email]);
    } else {
        // Criar novo admin
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha, tipo, status, email_verificado) VALUES (?, ?, ?, 'admin', 'ativo', TRUE)");
        $stmt->execute(['Administrador', $admin_email, $adminPasswordHash]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao configurar administrador: ' . $e->getMessage()]);
    exit;
}

// Criar arquivo de configuração
$configContent = "<?php
/**
 * Configuração do Banco de Dados
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * Gerado automaticamente pelo instalador em " . date('Y-m-d H:i:s') . "
 */

// Configurações do banco de dados
define('DB_HOST', '" . addslashes($db_host) . "');
define('DB_NAME', '" . addslashes($db_name) . "');
define('DB_USER', '" . addslashes($db_user) . "');
define('DB_PASS', '" . addslashes($db_pass) . "');
define('DB_CHARSET', 'utf8mb4');

/**
 * Conexão com o banco de dados usando PDO
 * @return PDO|null Retorna instância PDO ou null em caso de erro
 */
function getDBConnection() {
    static \$pdo = null;
    
    if (\$pdo === null) {
        try {
            \$dsn = \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=\" . DB_CHARSET;
            \$options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            \$pdo = new PDO(\$dsn, DB_USER, DB_PASS, \$options);
        } catch (PDOException \$e) {
            error_log(\"Erro de conexão com banco de dados: \" . \$e->getMessage());
            return null;
        }
    }
    
    return \$pdo;
}

/**
 * Testa a conexão com o banco de dados
 * @return bool True se conectado, false caso contrário
 */
function testDBConnection() {
    \$pdo = getDBConnection();
    return \$pdo !== null;
}
";

if (file_put_contents('../config/database.php', $configContent) === false) {
    echo json_encode(['success' => false, 'error' => 'Erro ao criar arquivo de configuração']);
    exit;
}

// Atualizar BASE_URL em constants.php
$constantsFile = '../config/constants.php';
if (file_exists($constantsFile)) {
    $constantsContent = file_get_contents($constantsFile);
    $constantsContent = preg_replace(
        "/define\('BASE_URL',\s*'[^']*'\);/",
        "define('BASE_URL', '" . addslashes($base_url) . "');",
        $constantsContent
    );
    file_put_contents($constantsFile, $constantsContent);
}

// Criar diretórios de upload
$uploadDirs = [
    '../uploads/laudos',
    '../uploads/documentos',
    '../uploads/logos',
    '../uploads/curriculos'
];

foreach ($uploadDirs as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Criar arquivo de marcação de instalação
file_put_contents('../.installed', date('Y-m-d H:i:s'));

// Testar conexão final
require_once '../config/database.php';
$connectionOk = testDBConnection();

if ($connectionOk) {
    echo json_encode([
        'success' => true,
        'message' => 'Instalação concluída com sucesso!',
        'redirect' => 'success.php?email=' . urlencode($admin_email)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Instalação concluída, mas não foi possível testar a conexão. Verifique manualmente.'
    ]);
}

