<?php
/**
 * Processamento de Cadastro de Empresa
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
    header('Location: cadastro-empresa.php');
    exit;
}

// Coletar e sanitizar dados do formulário
$razao_social = sanitizeInput($_POST['razao_social'] ?? '');
$nome_fantasia = sanitizeInput($_POST['nome_fantasia'] ?? '');
$cnpj = sanitizeInput($_POST['cnpj'] ?? '');
$data_fundacao = sanitizeInput($_POST['data_fundacao'] ?? '');
$porte_empresa = sanitizeInput($_POST['porte_empresa'] ?? '');
$setor = sanitizeInput($_POST['setor'] ?? '');
$website = sanitizeInput($_POST['website'] ?? '');
$descricao = sanitizeInput($_POST['descricao'] ?? '');

// Endereço
$cep = sanitizeInput($_POST['cep'] ?? '');
$estado = sanitizeInput($_POST['estado'] ?? '');
$cidade = sanitizeInput($_POST['cidade'] ?? '');
$bairro = sanitizeInput($_POST['bairro'] ?? '');
$logradouro = sanitizeInput($_POST['logradouro'] ?? '');
$numero = sanitizeInput($_POST['numero'] ?? '');
$complemento = sanitizeInput($_POST['complemento'] ?? '');

// Contato
$email_corporativo = sanitizeInput($_POST['email_corporativo'] ?? '');
$telefone_empresa = sanitizeInput($_POST['telefone_empresa'] ?? '');

// Responsável
$nome_responsavel = sanitizeInput($_POST['nome_responsavel'] ?? '');
$cargo_responsavel = sanitizeInput($_POST['cargo_responsavel'] ?? '');
$email_responsavel = sanitizeInput($_POST['email_responsavel'] ?? '');
$telefone_responsavel = sanitizeInput($_POST['telefone_responsavel'] ?? '');

// Compromisso com inclusão
$ja_contrata_pcd = isset($_POST['ja_contrata_pcd']) && $_POST['ja_contrata_pcd'] === 'sim';
$recursos_acessibilidade = isset($_POST['recursos_acessibilidade']) ? $_POST['recursos_acessibilidade'] : [];
$politica_inclusao = sanitizeInput($_POST['politica_inclusao'] ?? '');

// Gerar senha temporária
$senha_temporaria = bin2hex(random_bytes(8));
$senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);

// Validações
$errors = [];

if (empty($razao_social)) {
    $errors[] = "Razão social é obrigatória.";
}

if (empty($nome_fantasia)) {
    $errors[] = "Nome fantasia é obrigatório.";
}

if (empty($cnpj) || !validateCNPJ($cnpj)) {
    $errors[] = "CNPJ válido é obrigatório.";
}

if (empty($email_corporativo) || !validateEmail($email_corporativo)) {
    $errors[] = "Email corporativo válido é obrigatório.";
}

if (empty($porte_empresa)) {
    $errors[] = "Porte da empresa é obrigatório.";
}

if (empty($setor)) {
    $errors[] = "Setor de atuação é obrigatório.";
}

if (empty($nome_responsavel)) {
    $errors[] = "Nome do responsável é obrigatório.";
}

if (empty($cargo_responsavel)) {
    $errors[] = "Cargo do responsável é obrigatório.";
}

if (empty($email_responsavel) || !validateEmail($email_responsavel)) {
    $errors[] = "Email do responsável válido é obrigatório.";
}

// Processar upload de documentos
$documento_empresa_path = null;
if (isset($_FILES['documento_empresa']) && $_FILES['documento_empresa']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['documento_empresa'];
    
    if (!in_array($file['type'], ALLOWED_DOC_TYPES)) {
        $errors[] = "Tipo de arquivo inválido. Apenas PDF é permitido.";
    }
    
    if ($file['size'] > MAX_DOCUMENTO_SIZE) {
        $errors[] = "Arquivo muito grande. Tamanho máximo: 10MB.";
    }
    
    if (empty($errors)) {
        $upload_dir = UPLOADS_PATH . '/documentos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('doc_') . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $documento_empresa_path = 'uploads/documentos/' . $file_name;
        } else {
            $errors[] = "Erro ao fazer upload do documento.";
        }
    }
}

// Processar upload de logo
$logo_path = null;
if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['logo_empresa'];
    
    if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
        $errors[] = "Tipo de arquivo inválido para logo. Apenas JPG e PNG são permitidos.";
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $errors[] = "Logo muito grande. Tamanho máximo: 5MB.";
    }
    
    if (empty($errors)) {
        $upload_dir = UPLOADS_PATH . '/logos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('logo_') . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $logo_path = 'uploads/logos/' . $file_name;
        } else {
            $errors[] = "Erro ao fazer upload da logo.";
        }
    }
}

// Converter data de fundação
$data_fundacao_formatted = null;
if (!empty($data_fundacao)) {
    $date_parts = explode('/', $data_fundacao);
    if (count($date_parts) === 3) {
        $data_fundacao_formatted = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
    }
}

// Converter CNPJ (remover formatação)
$cnpj_cleaned = preg_replace('/[^0-9]/', '', $cnpj);

// Converter recursos de acessibilidade para JSON
$recursos_json = !empty($recursos_acessibilidade) ? json_encode($recursos_acessibilidade) : null;

// Se houver erros, redirecionar de volta
if (!empty($errors)) {
    $_SESSION['cadastro_empresa_errors'] = $errors;
    $_SESSION['cadastro_empresa_data'] = $_POST;
    header('Location: cadastro-empresa.php');
    exit;
}

// Inserir no banco de dados
$pdo = getDBConnection();
if (!$pdo) {
    $_SESSION['cadastro_empresa_errors'] = ['Erro de conexão com o banco de dados. Tente novamente mais tarde.'];
    header('Location: cadastro-empresa.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO companies (
            razao_social, nome_fantasia, cnpj, data_fundacao, porte_empresa, setor, website, descricao,
            cep, estado, cidade, bairro, logradouro, numero, complemento,
            email_corporativo, telefone_empresa,
            nome_responsavel, cargo_responsavel, email_responsavel, telefone_responsavel,
            ja_contrata_pcd, recursos_acessibilidade, politica_inclusao,
            documento_empresa_path, logo_path, senha, status
        ) VALUES (
            :razao_social, :nome_fantasia, :cnpj, :data_fundacao, :porte_empresa, :setor, :website, :descricao,
            :cep, :estado, :cidade, :bairro, :logradouro, :numero, :complemento,
            :email_corporativo, :telefone_empresa,
            :nome_responsavel, :cargo_responsavel, :email_responsavel, :telefone_responsavel,
            :ja_contrata_pcd, :recursos_acessibilidade, :politica_inclusao,
            :documento_empresa_path, :logo_path, :senha, 'ativa'
        )
    ");
    
    $stmt->execute([
        ':razao_social' => $razao_social,
        ':nome_fantasia' => $nome_fantasia,
        ':cnpj' => $cnpj_cleaned,
        ':data_fundacao' => $data_fundacao_formatted,
        ':porte_empresa' => $porte_empresa,
        ':setor' => $setor,
        ':website' => !empty($website) ? $website : null,
        ':descricao' => $descricao,
        ':cep' => !empty($cep) ? $cep : null,
        ':estado' => !empty($estado) ? $estado : null,
        ':cidade' => !empty($cidade) ? $cidade : null,
        ':bairro' => !empty($bairro) ? $bairro : null,
        ':logradouro' => !empty($logradouro) ? $logradouro : null,
        ':numero' => !empty($numero) ? $numero : null,
        ':complemento' => !empty($complemento) ? $complemento : null,
        ':email_corporativo' => $email_corporativo,
        ':telefone_empresa' => !empty($telefone_empresa) ? $telefone_empresa : null,
        ':nome_responsavel' => $nome_responsavel,
        ':cargo_responsavel' => $cargo_responsavel,
        ':email_responsavel' => $email_responsavel,
        ':telefone_responsavel' => !empty($telefone_responsavel) ? $telefone_responsavel : null,
        ':ja_contrata_pcd' => $ja_contrata_pcd,
        ':recursos_acessibilidade' => $recursos_json,
        ':politica_inclusao' => !empty($politica_inclusao) ? $politica_inclusao : null,
        ':documento_empresa_path' => $documento_empresa_path,
        ':logo_path' => $logo_path,
        ':senha' => $senha_hash
    ]);
    
    $company_id = $pdo->lastInsertId();
    
    // Sucesso
    $_SESSION['cadastro_empresa_success'] = true;
    $_SESSION['cadastro_empresa_message'] = 'Cadastro da empresa realizado com sucesso! Você já pode fazer login.';
    
    // TODO: Enviar email de confirmação
    
    header('Location: login.php?cadastro=success');
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao cadastrar empresa: " . $e->getMessage());
    
    if ($e->getCode() == 23000) {
        $_SESSION['cadastro_empresa_errors'] = ['CNPJ ou Email já cadastrado no sistema.'];
    } else {
        $_SESSION['cadastro_empresa_errors'] = ['Erro ao processar cadastro. Tente novamente mais tarde.'];
    }
    
    $_SESSION['cadastro_empresa_data'] = $_POST;
    header('Location: cadastro-empresa.php');
    exit;
}

