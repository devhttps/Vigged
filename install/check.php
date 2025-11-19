<?php
/**
 * Verificação de Pré-requisitos
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

function performSystemChecks() {
    $checks = [];
    
    // Verificar versão do PHP
    $phpVersion = phpversion();
    $checks[] = [
        'name' => 'Versão do PHP',
        'status' => version_compare($phpVersion, '7.4.0', '>=') ? 'ok' : 'error',
        'message' => version_compare($phpVersion, '7.4.0', '>=') ? "PHP $phpVersion" : "Requer PHP 7.4+ (atual: $phpVersion)"
    ];
    
    // Verificar extensões PHP
    $requiredExtensions = [
        'pdo' => 'PDO',
        'pdo_mysql' => 'PDO MySQL',
        'mbstring' => 'mbstring',
        'fileinfo' => 'fileinfo',
        'json' => 'JSON',
        'session' => 'Session'
    ];
    
    foreach ($requiredExtensions as $ext => $name) {
        $checks[] = [
            'name' => "Extensão $name",
            'status' => extension_loaded($ext) ? 'ok' : 'error',
            'message' => extension_loaded($ext) ? 'Instalada' : 'Não instalada'
        ];
    }
    
    // Verificar permissões de escrita
    $writableDirs = [
        '../config' => 'Diretório config/',
        '../uploads' => 'Diretório uploads/'
    ];
    
    foreach ($writableDirs as $dir => $name) {
        $exists = file_exists($dir);
        $writable = $exists && is_writable($dir);
        
        if (!$exists) {
            // Tentar criar
            @mkdir($dir, 0755, true);
            $exists = file_exists($dir);
            $writable = $exists && is_writable($dir);
        }
        
        $checks[] = [
            'name' => $name,
            'status' => $writable ? 'ok' : ($exists ? 'warning' : 'error'),
            'message' => $writable ? 'Gravável' : ($exists ? 'Sem permissão de escrita' : 'Não existe')
        ];
    }
    
    // Verificar se database.php já existe
    $dbConfigExists = file_exists('../config/database.php');
    $checks[] = [
        'name' => 'Arquivo de Configuração',
        'status' => $dbConfigExists ? 'warning' : 'ok',
        'message' => $dbConfigExists ? 'Já existe (será sobrescrito)' : 'Não existe (será criado)'
    ];
    
    // Verificar se já está instalado
    $alreadyInstalled = file_exists('../.installed');
    $checks[] = [
        'name' => 'Status da Instalação',
        'status' => $alreadyInstalled ? 'warning' : 'ok',
        'message' => $alreadyInstalled ? 'Já instalado' : 'Pronto para instalar'
    ];
    
    // Verificar limites de upload
    $uploadMax = ini_get('upload_max_filesize');
    $postMax = ini_get('post_max_size');
    $checks[] = [
        'name' => 'Limite de Upload',
        'status' => 'ok',
        'message' => "upload_max_filesize: $uploadMax, post_max_size: $postMax"
    ];
    
    // Verificar se database.sql existe
    $sqlFileExists = file_exists('../config/database.sql');
    $checks[] = [
        'name' => 'Arquivo SQL',
        'status' => $sqlFileExists ? 'ok' : 'error',
        'message' => $sqlFileExists ? 'Encontrado' : 'Não encontrado (config/database.sql)'
    ];
    
    return $checks;
}

