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
    
    // Preservar transparência para PNG
    if ($mimeType === 'image/png') {
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
    
    // Salvar imagem
    $success = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $success = imagejpeg($destinationImage, $destinationPath, $quality);
            break;
        case 'image/png':
            $success = imagepng($destinationImage, $destinationPath, 9);
            break;
        case 'image/gif':
            $success = imagegif($destinationImage, $destinationPath);
            break;
    }
    
    // Liberar memória
    imagedestroy($sourceImage);
    imagedestroy($destinationImage);
    
    return $success;
}

/**
 * Valida e processa upload de foto de perfil
 * @param array $file Array $_FILES['campo']
 * @param int $userId ID do usuário
 * @return string|false Caminho relativo do arquivo ou false em caso de erro
 */
function processProfilePhotoUpload($file, $userId) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Validar tipo
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    // Validar tamanho (máximo 5MB)
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return false;
    }
    
    // Criar diretório de uploads
    $uploadDir = UPLOADS_PATH . '/perfis/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Gerar nome único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $tempFileName = uniqid('perfil_' . $userId . '_') . '.' . $extension;
    $tempPath = $uploadDir . $tempFileName;
    
    // Mover arquivo temporário
    if (!move_uploaded_file($file['tmp_name'], $tempPath)) {
        return false;
    }
    
    // Redimensionar e otimizar
    $finalFileName = 'perfil_' . $userId . '_' . time() . '.jpg';
    $finalPath = $uploadDir . $finalFileName;
    
    if (resizeProfileImage($tempPath, $finalPath, 400, 400, 85)) {
        // Remover arquivo temporário
        @unlink($tempPath);
        
        // Retornar caminho relativo
        return 'uploads/perfis/' . $finalFileName;
    } else {
        // Se falhar redimensionamento, usar arquivo original
        @unlink($tempPath);
        return false;
    }
}

