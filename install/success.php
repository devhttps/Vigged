<?php
/**
 * Página de Sucesso da Instalação
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

// Verificar se está instalado
if (!file_exists('../.installed')) {
    header('Location: index.php');
    exit;
}

$adminEmail = $_GET['email'] ?? 'admin@vigged.com';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação Concluída - Vigged</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-2xl text-center">
            <div class="text-green-600 mb-6">
                <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Instalação Concluída!</h1>
            <p class="text-xl text-gray-600 mb-8">O Vigged foi instalado com sucesso em seu servidor.</p>
            
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-8 text-left">
                <h2 class="text-lg font-bold text-purple-900 mb-4">Credenciais de Acesso</h2>
                <div class="space-y-2">
                    <div>
                        <span class="font-medium text-gray-700">Email:</span>
                        <span class="text-gray-900 ml-2"><?php echo htmlspecialchars($adminEmail); ?></span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Senha:</span>
                        <span class="text-gray-900 ml-2">A senha que você configurou durante a instalação</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                <p class="text-yellow-800 text-sm">
                    <strong>⚠️ Importante:</strong> Por questões de segurança, remova o instalador após verificar que tudo está funcionando corretamente.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="../index.php" 
                    class="bg-purple-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-purple-700 transition">
                    Acessar o Sistema
                </a>
                <a href="../login.php" 
                    class="bg-white border-2 border-purple-600 text-purple-600 px-8 py-3 rounded-lg font-medium hover:bg-purple-50 transition">
                    Fazer Login
                </a>
                <a href="remove.php" 
                    class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-medium hover:bg-gray-300 transition">
                    Remover Instalador
                </a>
            </div>
            
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Próximos Passos</h3>
                <ul class="text-left space-y-2 text-gray-600">
                    <li>✅ Faça login com as credenciais do administrador</li>
                    <li>✅ Altere a senha padrão do administrador</li>
                    <li>✅ Configure seu perfil administrativo</li>
                    <li>✅ Revise as configurações de segurança</li>
                    <li>✅ Remova o instalador após verificar tudo</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

