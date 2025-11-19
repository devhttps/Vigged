<?php
/**
 * Instalador Web - Vigged
 * Plataforma de Inclusão e Oportunidades
 * 
 * Acesse: http://localhost/vigged/install
 */

// Verificar se já está instalado
if (file_exists('../config/database.php') && file_exists('../.installed')) {
    header('Location: ../index.php');
    exit;
}

// Verificar pré-requisitos
require_once 'check.php';
$checks = performSystemChecks();
$allChecksPassed = array_reduce($checks, function($carry, $check) {
    return $carry && $check['status'] === 'ok';
}, true);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador - Vigged</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .check-ok { color: #10b981; }
        .check-error { color: #ef4444; }
        .check-warning { color: #f59e0b; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-purple-600 mb-2">Vigged</h1>
                <p class="text-gray-600">Instalador da Plataforma de Inclusão e Oportunidades</p>
            </div>

            <!-- Verificação de Pré-requisitos -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Verificação de Pré-requisitos</h2>
                <div class="space-y-3">
                    <?php foreach ($checks as $check): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <?php if ($check['status'] === 'ok'): ?>
                                    <svg class="w-6 h-6 check-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                <?php elseif ($check['status'] === 'error'): ?>
                                    <svg class="w-6 h-6 check-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-6 h-6 check-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                <?php endif; ?>
                                <span class="font-medium"><?php echo htmlspecialchars($check['name']); ?></span>
                            </div>
                            <span class="text-sm <?php echo $check['status'] === 'ok' ? 'text-green-600' : ($check['status'] === 'error' ? 'text-red-600' : 'text-yellow-600'); ?>">
                                <?php echo htmlspecialchars($check['message']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!$allChecksPassed): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-red-800 font-medium">Corrija os erros acima antes de continuar a instalação.</p>
                </div>
            <?php endif; ?>

            <!-- Formulário de Instalação -->
            <form id="installForm" action="install.php" method="POST" class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Configuração do Banco de Dados</h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Host do Banco</label>
                        <input type="text" name="db_host" value="localhost" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Banco</label>
                        <input type="text" name="db_name" value="vigged_db" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usuário</label>
                        <input type="text" name="db_user" value="root" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                        <input type="password" name="db_pass" value=""
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-8">Configuração do Sistema</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL Base</label>
                    <?php
                    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                    $host = $_SERVER['HTTP_HOST'];
                    $scriptPath = dirname(dirname($_SERVER['SCRIPT_NAME']));
                    $baseUrl = $protocol . '://' . $host . ($scriptPath !== '/' ? $scriptPath : '');
                    ?>
                    <input type="text" name="base_url" value="<?php echo htmlspecialchars($baseUrl); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">Exemplo: http://localhost/vigged ou https://seu-dominio.com</p>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-8">Administrador Padrão</h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="admin_email" value="admin@vigged.com" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                        <input type="password" name="admin_password" value="admin123" required minlength="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Mínimo 6 caracteres</p>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800 text-sm">
                        <strong>⚠️ Importante:</strong> Altere a senha do administrador após o primeiro login!
                    </p>
                </div>

                <div class="flex items-center space-x-4 pt-4">
                    <button type="submit" 
                        <?php echo !$allChecksPassed ? 'disabled' : ''; ?>
                        class="flex-1 bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Instalar Vigged
                    </button>
                    <button type="button" onclick="location.reload()" 
                        class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Verificar Novamente
                    </button>
                </div>
            </form>

            <!-- Progress Indicator (hidden until form submit) -->
            <div id="progressIndicator" class="hidden mt-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                        <p class="text-blue-800 font-medium">Instalando... Por favor, aguarde.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('installForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const progressIndicator = document.getElementById('progressIndicator');
            progressIndicator.classList.remove('hidden');
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('install.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Redirecionar para página de sucesso
                    window.location.href = result.redirect || 'success.php';
                } else {
                    // Mostrar erros
                    progressIndicator.classList.add('hidden');
                    submitBtn.disabled = false;
                    
                    let errorMsg = result.error || 'Erro desconhecido';
                    if (result.errors && result.errors.length > 0) {
                        errorMsg = result.errors.join('<br>');
                    }
                    
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mb-6';
                    errorDiv.innerHTML = '<p class="text-red-800 font-medium">Erro na instalação:</p><p class="text-red-700 mt-2">' + errorMsg + '</p>';
                    
                    this.insertBefore(errorDiv, this.firstChild);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } catch (error) {
                progressIndicator.classList.add('hidden');
                submitBtn.disabled = false;
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mb-6';
                errorDiv.innerHTML = '<p class="text-red-800 font-medium">Erro ao processar instalação:</p><p class="text-red-700 mt-2">' + error.message + '</p>';
                
                this.insertBefore(errorDiv, this.firstChild);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    </script>
</body>
</html>

