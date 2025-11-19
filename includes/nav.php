<?php
/**
 * Navegação comum para todas as páginas
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * @param string $navType Tipo de navegação: 'public' (padrão) ou 'authenticated'
 */
$navType = isset($navType) ? $navType : 'public';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Navigation -->
<nav class="bg-purple-600 text-white <?php echo $navType === 'authenticated' ? '' : 'h-16'; ?>">
    <div class="max-w-7xl mx-auto px-4 <?php echo $navType === 'authenticated' ? 'sm:px-6 lg:px-8' : ''; ?> <?php echo $navType === 'authenticated' ? '' : 'h-full'; ?> flex items-center justify-between">
        <div class="flex items-center <?php echo $navType === 'authenticated' ? 'space-x-8' : ''; ?>">
            <a href="index.php" class="text-2xl font-bold">Vigged</a>
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="hover:text-purple-200 transition <?php echo $currentPage === 'index.php' ? 'text-purple-200 font-semibold' : ''; ?>">Início</a>
                <a href="vagas.php" class="hover:text-purple-200 transition <?php echo $currentPage === 'vagas.php' ? 'text-purple-200 font-semibold' : ''; ?>">Vagas</a>
                <a href="empresas.php" class="hover:text-purple-200 transition <?php echo $currentPage === 'empresas.php' ? 'text-purple-200 font-semibold' : ''; ?>">Empresas</a>
                <a href="sobre-nos.php" class="hover:text-purple-200 transition <?php echo $currentPage === 'sobre-nos.php' ? 'text-purple-200 font-semibold' : ''; ?>">Sobre nós</a>
                <a href="suporte.php" class="hover:text-purple-200 transition <?php echo $currentPage === 'suporte.php' ? 'text-purple-200 font-semibold' : ''; ?>">Contato</a>
            </div>
        </div>
        <div class="flex space-x-3">
            <?php if ($navType === 'public'): ?>
                <a href="login.php" class="px-4 py-2 border border-white rounded-lg hover:bg-white hover:text-purple-600 transition">Login</a>
                <a href="pre-cadastro.php" class="px-4 py-2 bg-white text-purple-600 rounded-lg hover:bg-purple-50 transition">Cadastrar-se</a>
            <?php else: ?>
                <button onclick="logout()" class="px-6 py-2 bg-white text-purple-600 rounded-lg hover:bg-purple-50 transition font-medium">
                    Sair
                </button>
            <?php endif; ?>
        </div>
    </div>
</nav>

