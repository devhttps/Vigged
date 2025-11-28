<?php
/**
 * Navegação comum para todas as páginas
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * @param string $navType Tipo de navegação: 'public' (padrão) ou 'authenticated'
 */

// Iniciar sessão se ainda não foi iniciada
if (!isset($sessionStarted)) {
    require_once __DIR__ . '/../config/constants.php';
    require_once __DIR__ . '/../config/auth.php';
    startSecureSession();
    $sessionStarted = true;
}

// Verificar se usuário está autenticado
$isAuthenticated = isAuthenticated();
$currentUser = null;
if ($isAuthenticated) {
    $currentUser = getCurrentUser();
}

// Determinar tipo de navegação baseado na autenticação
$navType = isset($navType) ? $navType : ($isAuthenticated ? 'authenticated' : 'public');
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Navigation -->
<!-- Navigation -->
<nav class="bg-purple-600 text-white h-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">
        <div class="flex items-center space-x-8">
            <a href="index.php" class="text-2xl font-bold">Vigged</a>

            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="hover:text-purple-200 transition <?php echo $currentPage === 'index.php' ? 'text-purple-200 font-semibold' : ''; ?>">Início</a>
                <a href="vagas.php" class="hover:text-purple-200 transition <?php echo $currentPage === 'vagas.php' ? 'text-purple-200 font-semibold' : ''; ?>">Vagas</a>
                <a href="empresas.php" class="hover:text-purple-200 transition <?php echo $currentPage === 'empresas.php' ? 'text-purple-200 font-semibold' : ''; ?>">Empresas</a>
                <a href="sobre-nos.php" class="hover:text-purple-200 transition <?php echo $currentPage === 'sobre-nos.php' ? 'text-purple-200 font-semibold' : ''; ?>">Sobre nós</a>
                <a href="suporte.php" class="hover:text-purple-200 transition <?php echo $currentPage === 'suporte.php' ? 'text-purple-200 font-semibold' : ''; ?>">Contato</a>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <?php if ($isAuthenticated): ?>
                <!-- Usuário autenticado -->
                <!-- Notificações -->
                <div class="relative">
                    <button onclick="toggleNotifications()" class="relative p-2 hover:bg-purple-700 rounded-lg transition">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="notificationBadge" class="hidden absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </button>
                    <div id="notificationsPanel" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900">Notificações</h3>
                            <button onclick="markAllNotificationsRead()" class="text-sm text-purple-600 hover:text-purple-700">Marcar todas como lidas</button>
                        </div>
                        <div id="notificationsList" class="divide-y divide-gray-200">
                            <p class="p-4 text-gray-500 text-center">Carregando...</p>
                        </div>
                    </div>
                </div>
                <span class="text-sm text-purple-100 hidden md:block">
                    <?php 
                    if ($currentUser) {
                        $userName = $currentUser['nome'] ?? $currentUser['razao_social'] ?? $currentUser['nome_fantasia'] ?? 'Usuário';
                        echo htmlspecialchars($userName);
                    }
                    ?>
                </span>
                <?php
                // Mostrar link para perfil baseado no tipo de usuário
                $userType = $_SESSION['user_type'] ?? '';
                if ($userType === USER_TYPE_ADMIN) {
                    echo '<a href="admin.php" class="px-4 py-2 border border-white rounded-lg hover:bg-white hover:text-purple-600 transition text-sm">Admin</a>';
                } elseif ($userType === USER_TYPE_COMPANY) {
                    echo '<a href="perfil-empresa.php" class="px-4 py-2 border border-white rounded-lg hover:bg-white hover:text-purple-600 transition text-sm">Perfil</a>';
                } else {
                    echo '<a href="perfil-pcd.php" class="px-4 py-2 border border-white rounded-lg hover:bg-white hover:text-purple-600 transition text-sm">Perfil</a>';
                }
                ?>
                <a href="logout.php" class="px-6 py-2 bg-white text-purple-600 rounded-lg hover:bg-purple-50 transition font-medium">
                    Sair
                </a>
            <?php else: ?>
                <!-- Usuário não autenticado -->
                <a href="login.php" class="px-4 py-2 border border-white rounded-lg hover:bg-white hover:text-purple-600 transition">Login</a>
                <a href="login.php" class="px-4 py-2 bg-white text-purple-600 rounded-lg hover:bg-purple-50 transition">Criar Conta</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

