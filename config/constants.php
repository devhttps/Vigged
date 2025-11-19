<?php
/**
 * Constantes do Sistema
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

// Diretórios
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// URLs
define('BASE_URL', 'http://localhost/vigged'); // Ajustar conforme ambiente
define('ASSETS_URL', BASE_URL . '/assets');

// Configurações de Sessão
define('SESSION_NAME', 'vigged_session');
define('SESSION_LIFETIME', 3600 * 24); // 24 horas

// Tipos de Usuário
define('USER_TYPE_PCD', 'pcd');
define('USER_TYPE_COMPANY', 'company');
define('USER_TYPE_ADMIN', 'admin');

// Status de Usuário
define('STATUS_ATIVO', 'ativo');
define('STATUS_INATIVO', 'inativo');
define('STATUS_PENDENTE', 'pendente');

// Status de Vaga
define('JOB_STATUS_ATIVA', 'ativa');
define('JOB_STATUS_PAUSADA', 'pausada');
define('JOB_STATUS_ENCERRADA', 'encerrada');

// Status de Candidatura
define('APP_STATUS_PENDENTE', 'pendente');
define('APP_STATUS_EM_ANALISE', 'em_analise');
define('APP_STATUS_APROVADA', 'aprovada');
define('APP_STATUS_REJEITADA', 'rejeitada');
define('APP_STATUS_CANCELADA', 'cancelada');

// Planos de Empresa
define('PLANO_GRATUITO', 'gratuito');
define('PLANO_ESSENCIAL', 'essencial');
define('PLANO_PROFISSIONAL', 'profissional');
define('PLANO_ENTERPRISE', 'enterprise');

// Limites de Upload
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_LAUDO_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_DOCUMENTO_SIZE', 10 * 1024 * 1024); // 10MB

// Tipos de Arquivo Permitidos
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg']);
define('ALLOWED_DOC_TYPES', ['application/pdf']);

// Configurações de Segurança
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutos

// Paginação
define('ITEMS_PER_PAGE', 10);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Configurações de Email (a implementar)
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('EMAIL_FROM', 'noreply@vigged.com.br');
define('EMAIL_FROM_NAME', 'Vigged');

// Timezone
date_default_timezone_set('America/Sao_Paulo');

