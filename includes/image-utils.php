<?php
/**
 * Utilitários para Manipulação de Imagens
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once __DIR__ . '/../config/constants.php';

/**
 * Redimensiona e otimiza imagem de perfil
 * @param string $sourcePath Caminho da imagem original
 * @param string $destinationPath Caminho de destino
 * @param int $maxWidth Largura máxima (padrão: 400px)
 * @param int $maxHeight Altura máxima (padrão: 400px)
 * @param int $quality Qualidade JPEG (1-100, padrão: 85)
 * @return bool True se sucesso, false caso contrário
 */
function resizeProfileImage($sourcePath, $destinationPath, $maxWidth = 400, $maxHeight = 400, $quality = 85) {
    if (!file_exists($sourcePath)) {
        return false;
    }
    
    // Verificar se GD está disponível
    if (!extension_loaded('gd')) {
        error_log("GD extension não está disponível");
        return false;
    }
    
    // Obter informações da imagem
    $imageInfo = getimagesize($sourcePath);
    if ($imageInfo === false) {
        return false;
    }
    
    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];
    
    // Criar imagem a partir do arquivo
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            if (function_exists('imagecreatefromwebp')) {
                $sourceImage = imagecreatefromwebp($sourcePath);
            } else {
                error_log("WebP não suportado - extensão GD não tem suporte a WebP");
                return false;
            }
            break;
        default:
            return false;
    }
    
    if ($sourceImage === false) {
        return false;
    }
    
    // Calcular novas dimensões mantendo proporção
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = (int)($originalWidth * $ratio);
    $newHeight = (int)($originalHeight * $ratio);
    
    // Criar nova imagem redimensionada
    $destinationImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preservar transparência para PNG e WebP
    if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
        imagealphablending($destinationImage, false);
        imagesavealpha($destinationImage, true);
        $transparent = imagecolorallocatealpha($destinationImage, 255, 255, 255, 127);
        imagefilledrectangle($destinationImage, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Redimensionar imagem
    imagecopyresampled(
        $destinationImage, $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $originalWidth, $originalHeight
    );
    
    // Criar diretório se não existir
    $destinationDir = dirname($destinationPath);
    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0755, true);
    }
    
    // Salvar imagem (sempre salvar como JPEG para perfil, independente do formato original)
    $success = imagejpeg($destinationImage, $destinationPath, $quality);
    
    // Liberar memória
    imagedestroy($sourceImage);
    imagedestroy($destinationImage);
    
    return $success;
}

/**
 * Valida e processa upload de foto de perfil
 * @param array $file Array $_FILES['campo']
 * @param int $userId ID do usuário
 * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
 */
function processProfilePhotoUpload($file, $userId) {
    // Validar erro de upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'O arquivo excede o tamanho máximo permitido pelo servidor.',
            UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o tamanho máximo permitido pelo formulário.',
            UPLOAD_ERR_PARTIAL => 'O arquivo foi enviado parcialmente.',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta uma pasta temporária.',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o arquivo no disco.',
            UPLOAD_ERR_EXTENSION => 'Uma extensão PHP interrompeu o upload do arquivo.'
        ];
        return [
            'success' => false,
            'path' => null,
            'error' => $errorMessages[$file['error']] ?? 'Erro desconhecido no upload.'
        ];
    }
    
    // Validar se arquivo foi enviado
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return [
            'success' => false,
            'path' => null,
            'error' => 'Arquivo inválido ou não foi enviado corretamente.'
        ];
    }
    
    // Validar extensão do arquivo
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        return [
            'success' => false,
            'path' => null,
            'error' => 'Formato de arquivo não permitido. Use apenas JPG, PNG, GIF ou WebP.'
        ];
    }
    
    // Validar tipo MIME
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedMimeTypes)) {
        return [
            'success' => false,
            'path' => null,
            'error' => 'Tipo de arquivo inválido. O arquivo deve ser uma imagem válida.'
        ];
    }
    
    // Validar tamanho (máximo 5MB)
    $maxSize = defined('MAX_UPLOAD_SIZE') ? MAX_UPLOAD_SIZE : (5 * 1024 * 1024); // 5MB padrão
    if ($file['size'] > $maxSize) {
        $maxSizeMB = round($maxSize / 1024 / 1024, 2);
        return [
            'success' => false,
            'path' => null,
            'error' => "O arquivo excede o tamanho máximo de {$maxSizeMB}MB."
        ];
    }
    
    // Validar se é realmente uma imagem usando getimagesize
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return [
            'success' => false,
            'path' => null,
            'error' => 'O arquivo não é uma imagem válida.'
        ];
    }
    
    // Validar dimensões mínimas e máximas
    $minWidth = 100;
    $minHeight = 100;
    $maxWidth = 5000;
    $maxHeight = 5000;
    
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    
    if ($width < $minWidth || $height < $minHeight) {
        return [
            'success' => false,
            'path' => null,
            'error' => "A imagem deve ter no mínimo {$minWidth}x{$minHeight} pixels."
        ];
    }
    
    if ($width > $maxWidth || $height > $maxHeight) {
        return [
            'success' => false,
            'path' => null,
            'error' => "A imagem excede as dimensões máximas de {$maxWidth}x{$maxHeight} pixels."
        ];
    }
    
    // Criar diretório de uploads
    $uploadDir = UPLOADS_PATH . '/perfis/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'Erro ao criar diretório de uploads.'
            ];
        }
    }
    
    // Verificar se o diretório é gravável
    if (!is_writable($uploadDir)) {
        return [
            'success' => false,
            'path' => null,
            'error' => 'Diretório de uploads não tem permissão de escrita.'
        ];
    }
    
    // Gerar nome único
    $tempFileName = uniqid('perfil_' . $userId . '_') . '.' . $extension;
    $tempPath = $uploadDir . $tempFileName;
    
    // Mover arquivo temporário
    if (!move_uploaded_file($file['tmp_name'], $tempPath)) {
        return [
            'success' => false,
            'path' => null,
            'error' => 'Erro ao salvar o arquivo no servidor.'
        ];
    }
    
    // Redimensionar e otimizar
    $finalFileName = 'perfil_' . $userId . '_' . time() . '.jpg';
    $finalPath = $uploadDir . $finalFileName;
    
    if (resizeProfileImage($tempPath, $finalPath, 400, 400, 85)) {
        // Remover arquivo temporário
        @unlink($tempPath);
        
        // Retornar caminho relativo
        return [
            'success' => true,
            'path' => 'uploads/perfis/' . $finalFileName,
            'error' => null
        ];
    } else {
        // Se falhar redimensionamento, tentar usar arquivo original (se for JPEG)
        @unlink($tempPath);
        return [
            'success' => false,
            'path' => null,
            'error' => 'Erro ao processar a imagem. Verifique se a extensão GD está habilitada.'
        ];
    }
}

