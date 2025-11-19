<?php
/**
 * Remover Instalador
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * Acesse: http://localhost/vigged/install/remove.php
 * 
 * ⚠️ ATENÇÃO: Este arquivo remove o instalador após a instalação completa.
 * Execute apenas se tiver certeza de que a instalação foi bem-sucedida.
 */

// Verificar se está instalado
if (!file_exists('../.installed')) {
    die('Sistema não está instalado. Execute a instalação primeiro.');
}

// Verificar se é requisição POST com confirmação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    // Remover diretório do instalador
    function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
    
    $removed = deleteDirectory(__DIR__);
    
    if ($removed) {
        echo '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador Removido - Vigged</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-md text-center">
        <div class="text-green-600 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Instalador Removido</h1>
        <p class="text-gray-600 mb-6">O instalador foi removido com sucesso por questões de segurança.</p>
        <a href="../index.php" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
            Ir para o Sistema
        </a>
    </div>
</body>
</html>';
        exit;
    } else {
        $error = 'Erro ao remover instalador. Remova manualmente a pasta install/';
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remover Instalador - Vigged</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-md">
        <div class="text-yellow-600 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-4 text-center">Remover Instalador</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <p class="text-red-800"><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-yellow-800 text-sm">
                <strong>⚠️ Atenção:</strong> Esta ação irá remover permanentemente o instalador por questões de segurança.
                Certifique-se de que a instalação foi concluída com sucesso antes de continuar.
            </p>
        </div>
        
        <form method="POST" class="space-y-4">
            <div class="flex items-center">
                <input type="checkbox" id="confirm" name="confirm" value="yes" required
                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <label for="confirm" class="ml-2 text-sm text-gray-700">
                    Confirmo que a instalação foi concluída com sucesso
                </label>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" 
                    class="flex-1 bg-red-600 text-white py-3 rounded-lg font-medium hover:bg-red-700 transition">
                    Remover Instalador
                </button>
                <a href="../index.php" 
                    class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</body>
</html>

