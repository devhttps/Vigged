<?php
/**
 * Processamento de Cadastro de Candidato PCD
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'config/auth.php';
require_once 'includes/functions.php';

// Iniciar sessão segura
startSecureSession();

// Verificar se é requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

// Coletar e sanitizar dados do formulário
$nome = sanitizeInput($_POST['nome'] ?? '');
$cpf = sanitizeInput($_POST['cpf'] ?? '');
$data_nascimento = sanitizeInput($_POST['data_nascimento'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$telefone = sanitizeInput($_POST['telefone'] ?? '');
$tipo_deficiencia = sanitizeInput($_POST['tipo_deficiencia'] ?? '');
$especifique_outra = sanitizeInput($_POST['especifique_outra'] ?? '');
$cid = sanitizeInput($_POST['cid'] ?? '');
$possui_laudo = isset($_POST['possui_laudo']) && $_POST['possui_laudo'] === 'sim';
$recursos_acessibilidade = isset($_POST['recursos']) ? $_POST['recursos'] : [];
$outras_necessidades = sanitizeInput($_POST['outras_necessidades'] ?? '');

// Gerar senha temporária (será alterada no primeiro login)
$senha_temporaria = bin2hex(random_bytes(8));
$senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);

// Validações
$errors = [];

if (empty($nome)) {
    $errors[] = "Nome completo é obrigatório.";
}

if (empty($email) || !validateEmail($email)) {
    $errors[] = "Email válido é obrigatório.";
}

if (!empty($cpf) && !validateCPF($cpf)) {
    $errors[] = "CPF inválido.";
}

if (empty($tipo_deficiencia)) {
    $errors[] = "Tipo de deficiência é obrigatório.";
}

// Processar upload de laudo médico
$laudo_medico_path = null;
if ($possui_laudo && isset($_FILES['laudo_medico']) && $_FILES['laudo_medico']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['laudo_medico'];
    
    // Validar tipo de arquivo
    $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!in_array($file['type'], $allowed_types)) {
        $errors[] = "Tipo de arquivo inválido. Apenas PDF, JPG e PNG são permitidos.";
    }
    
    // Validar tamanho
    if ($file['size'] > MAX_LAUDO_SIZE) {
        $errors[] = "Arquivo muito grande. Tamanho máximo: 5MB.";
    }
    
    if (empty($errors)) {
        // Criar diretório de uploads se não existir
        $upload_dir = UPLOADS_PATH . '/laudos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Gerar nome único para o arquivo
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('laudo_') . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $laudo_medico_path = 'uploads/laudos/' . $file_name;
        } else {
            $errors[] = "Erro ao fazer upload do arquivo.";
        }
    }
}

// Converter data de nascimento
$data_nascimento_formatted = null;
if (!empty($data_nascimento)) {
    $date_parts = explode('/', $data_nascimento);
    if (count($date_parts) === 3) {
        $data_nascimento_formatted = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
    }
}

// Converter CPF (remover formatação)
$cpf_cleaned = preg_replace('/[^0-9]/', '', $cpf);

// Converter recursos de acessibilidade para JSON
$recursos_json = !empty($recursos_acessibilidade) ? json_encode($recursos_acessibilidade) : null;

// Se houver erros, redirecionar de volta com mensagens
if (!empty($errors)) {
    $_SESSION['cadastro_errors'] = $errors;
    $_SESSION['cadastro_data'] = $_POST;
    header('Location: cadastro.php');
    exit;
}

// Inserir no banco de dados
$pdo = getDBConnection();
if (!$pdo) {
    $_SESSION['cadastro_errors'] = ['Erro de conexão com o banco de dados. Tente novamente mais tarde.'];
    header('Location: cadastro.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO users (
            nome, email, senha, tipo, cpf, telefone, data_nascimento,
            tipo_deficiencia, especifique_outra, cid, possui_laudo, laudo_medico_path,
            recursos_acessibilidade, outras_necessidades, status
        ) VALUES (
            :nome, :email, :senha, 'pcd', :cpf, :telefone, :data_nascimento,
            :tipo_deficiencia, :especifique_outra, :cid, :possui_laudo, :laudo_medico_path,
            :recursos_acessibilidade, :outras_necessidades, 'ativo'
        )
    ");
    
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senha_hash,
        ':cpf' => !empty($cpf_cleaned) ? $cpf_cleaned : null,
        ':telefone' => !empty($telefone) ? $telefone : null,
        ':data_nascimento' => $data_nascimento_formatted,
        ':tipo_deficiencia' => $tipo_deficiencia,
        ':especifique_outra' => !empty($especifique_outra) ? $especifique_outra : null,
        ':cid' => !empty($cid) ? $cid : null,
        ':possui_laudo' => $possui_laudo,
        ':laudo_medico_path' => $laudo_medico_path,
        ':recursos_acessibilidade' => $recursos_json,
        ':outras_necessidades' => !empty($outras_necessidades) ? $outras_necessidades : null
    ]);
    
    $user_id = $pdo->lastInsertId();
    
    // Sucesso - redirecionar para página de sucesso ou login
    $_SESSION['cadastro_success'] = true;
    $_SESSION['cadastro_message'] = 'Cadastro realizado com sucesso! Você já pode fazer login.';
    
    // TODO: Enviar email de confirmação
    
    header('Location: login.php?cadastro=success');
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao cadastrar usuário: " . $e->getMessage());
    error_log("SQL Error Code: " . $e->getCode());
    error_log("SQL Error Info: " . print_r($e->errorInfo, true));
    
    // Verificar se é erro de duplicação
    if ($e->getCode() == 23000 || strpos($e->getMessage(), 'Duplicate') !== false) {
        $_SESSION['cadastro_errors'] = ['Email ou CPF já cadastrado no sistema.'];
    } else {
        // Em desenvolvimento, mostrar erro detalhado
        $errorMsg = 'Erro ao processar cadastro. Tente novamente mais tarde.';
        if (defined('DEBUG') && DEBUG) {
            $errorMsg .= ' Detalhes: ' . $e->getMessage();
        }
        $_SESSION['cadastro_errors'] = [$errorMsg];
    }
    
    $_SESSION['cadastro_data'] = $_POST;
    header('Location: cadastro.php');
    exit;
}

