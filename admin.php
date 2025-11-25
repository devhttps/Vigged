<?php
// Configurar título da página
$title = 'Painel Administrativo';

// Estilos customizados para admin (sidebar, modais, etc)
$additionalStyles = [
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">',
    '<style>
        .sidebar-link:hover {
            background-color: rgba(124, 58, 237, 0.1);
        }
        .sidebar-link.active {
            background-color: rgba(124, 58, 237, 0.2);
            border-left: 4px solid #7c3aed;
        }
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>'
];

// Verificar autenticação
require_once 'config/auth.php';
startSecureSession();
requireAdmin();

// Incluir head
include 'includes/head.php';
?>
    <!-- Header -->
    <header class="bg-gradient-to-r from-purple-600 to-purple-700 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-8">
                    <h1 class="text-2xl font-bold">Vigged Admin</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Administrador</span>
                    <a href="logout.php" class="bg-white text-purple-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition text-sm font-medium">
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white h-screen shadow-lg sticky top-0">
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="#dashboard" class="sidebar-link active flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#usuarios" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-users w-5"></i>
                            <span>Usuários PCD</span>
                        </a>
                    </li>
                    <li>
                        <a href="#empresas" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-building w-5"></i>
                            <span>Empresas</span>
                        </a>
                    </li>
                    <li>
                        <a href="#vagas" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-briefcase w-5"></i>
                            <span>Vagas</span>
                        </a>
                    </li>
                    <li>
                        <a href="#planos" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-crown w-5"></i>
                            <span>Planos Pendentes</span>
                            <span id="planosBadge" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden">0</span>
                        </a>
                    </li>
                    <li>
                        <a href="#gerenciar-planos" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-credit-card w-5"></i>
                            <span>Gerenciar Planos</span>
                        </a>
                    </li>
                    <li>
                        <a href="#relatorios" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-file-alt w-5"></i>
                            <span>Relatórios</span>
                        </a>
                    </li>
                    <li>
                        <a href="#configuracoes" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-cog w-5"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Dashboard Section -->
            <section id="dashboard-section">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
                    <div class="flex items-center space-x-2">
                        <button onclick="refreshDashboard()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm">
                            <i class="fas fa-sync-alt mr-2"></i>Atualizar
                        </button>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm mb-1">Total de Usuários PCD</p>
                                <p id="total-users" class="text-4xl font-bold">0</p>
                                <p id="users-growth" class="text-purple-100 text-xs mt-2">
                                    <i class="fas fa-chart-line"></i> <span>Carregando...</span>
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm mb-1">Empresas Cadastradas</p>
                                <p id="total-companies" class="text-4xl font-bold">0</p>
                                <p id="companies-growth" class="text-blue-100 text-xs mt-2">
                                    <i class="fas fa-chart-line"></i> <span>Carregando...</span>
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                                <i class="fas fa-building text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm mb-1">Vagas Ativas</p>
                                <p id="total-jobs" class="text-4xl font-bold">0</p>
                                <p class="text-green-100 text-xs mt-2">
                                    <span id="jobs-status"></span>
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                                <i class="fas fa-briefcase text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-gradient-to-br from-orange-500 to-orange-600 text-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm mb-1">Total de Candidaturas</p>
                                <p id="total-applications" class="text-4xl font-bold">0</p>
                                <p id="applications-growth" class="text-orange-100 text-xs mt-2">
                                    <i class="fas fa-chart-line"></i> <span>Carregando...</span>
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                                <i class="fas fa-file-alt text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Taxa de Conversão</p>
                                <p id="conversion-rate" class="text-2xl font-bold text-purple-600">0%</p>
                                <p class="text-gray-500 text-xs mt-1">Candidaturas por vaga</p>
                            </div>
                            <i class="fas fa-percentage text-purple-500 text-3xl"></i>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Taxa de Aprovação</p>
                                <p id="approval-rate" class="text-2xl font-bold text-green-600">0%</p>
                                <p class="text-gray-500 text-xs mt-1">Candidaturas aprovadas</p>
                            </div>
                            <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Candidaturas Aprovadas</p>
                                <p id="approved-applications" class="text-2xl font-bold text-blue-600">0</p>
                                <p class="text-gray-500 text-xs mt-1">Total de aprovações</p>
                            </div>
                            <i class="fas fa-thumbs-up text-blue-500 text-3xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Charts and Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Growth Chart -->
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Crescimento Mensal</h3>
                        <div id="growth-chart" class="h-64 flex items-end justify-between space-x-2">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Distribution -->
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Distribuição por Status</h3>
                        <div id="status-distribution" class="space-y-4">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Cadastros Recentes</h3>
                            <a href="#usuarios" onclick="document.querySelector('a[href=\"#usuarios\"]').click()" class="text-purple-600 text-sm hover:underline">Ver todos</a>
                        </div>
                        <div id="recent-registrations" class="space-y-3">
                            <div class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Vagas Mais Populares</h3>
                            <a href="#vagas" onclick="document.querySelector('a[href=\"#vagas\"]').click()" class="text-purple-600 text-sm hover:underline">Ver todas</a>
                        </div>
                        <div id="popular-jobs" class="space-y-3">
                            <div class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Users Section -->
            <section id="usuarios-section" class="hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Usuários PCD</h2>
                    <button onclick="openUserModal()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-plus mr-2"></i>Adicionar Usuário
                    </button>
                </div>

                <!-- Search and Filter -->
                <div class="bg-white p-4 rounded-xl shadow-md mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" id="user-search" placeholder="Buscar por nome..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <select id="user-disability-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Tipo de Deficiência</option>
                            <option value="Deficiência Física">Deficiência Física</option>
                            <option value="Deficiência Visual">Deficiência Visual</option>
                            <option value="Deficiência Auditiva">Deficiência Auditiva</option>
                            <option value="Deficiência Intelectual">Deficiência Intelectual</option>
                        </select>
                        <select id="user-status-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Status</option>
                            <option value="Ativo">Ativo</option>
                            <option value="Inativo">Inativo</option>
                            <option value="Pendente">Pendente</option>
                        </select>
                        <button onclick="filterUsers()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Deficiência</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Cadastro</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-6">
                    <p class="text-sm text-gray-700">
                        Mostrando <span id="users-showing-start">0</span> a <span id="users-showing-end">0</span> de <span id="users-total">0</span> resultados
                    </p>
                    <div id="users-pagination" class="flex space-x-2">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </section>

            <!-- Configurações Section -->
            <section id="configuracoes-section" class="hidden">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Configurações do Sistema</h2>
                
                <!-- Tabs -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="flex space-x-8">
                        <button onclick="showConfigTab('geral')" id="tab-geral" class="config-tab active py-4 px-1 border-b-2 border-purple-600 font-medium text-purple-600">
                            Geral
                        </button>
                        <button onclick="showConfigTab('email')" id="tab-email" class="config-tab py-4 px-1 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700">
                            Email
                        </button>
                        <button onclick="showConfigTab('upload')" id="tab-upload" class="config-tab py-4 px-1 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700">
                            Upload
                        </button>
                        <button onclick="showConfigTab('seguranca')" id="tab-seguranca" class="config-tab py-4 px-1 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700">
                            Segurança
                        </button>
                        <button onclick="showConfigTab('sistema')" id="tab-sistema" class="config-tab py-4 px-1 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700">
                            Informações do Sistema
                        </button>
                    </nav>
                </div>

                <!-- Tab: Geral -->
                <div id="config-geral" class="config-tab-content">
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Configurações Gerais</h3>
                        <form id="config-geral-form" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Site</label>
                                <input type="text" id="site_name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descrição do Site</label>
                                <textarea id="site_description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">URL Base</label>
                                <input type="url" id="base_url" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <p class="text-sm text-gray-500 mt-1">Exemplo: http://localhost/vigged ou https://seu-dominio.com</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fuso Horário</label>
                                <select id="timezone" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="America/Sao_Paulo">America/Sao_Paulo (Brasil)</option>
                                    <option value="America/Manaus">America/Manaus</option>
                                    <option value="America/Rio_Branco">America/Rio_Branco</option>
                                    <option value="UTC">UTC</option>
                                </select>
                            </div>
                            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                                Salvar Configurações Gerais
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab: Email -->
                <div id="config-email" class="config-tab-content hidden">
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Configurações de Email (SMTP)</h3>
                        <form id="config-email-form" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Servidor SMTP</label>
                                    <input type="text" id="smtp_host" placeholder="smtp.gmail.com" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Porta SMTP</label>
                                    <input type="number" id="smtp_port" value="587" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Usuário SMTP</label>
                                    <input type="text" id="smtp_user" placeholder="seu-email@gmail.com" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Senha SMTP</label>
                                    <input type="password" id="smtp_pass" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Remetente</label>
                                    <input type="email" id="from_email" placeholder="noreply@vigged.com.br" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Remetente</label>
                                    <input type="text" id="from_name" value="Vigged" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-yellow-800 text-sm">
                                    <strong>⚠️ Importante:</strong> Para Gmail, use uma senha de aplicativo em vez da senha da conta.
                                </p>
                            </div>
                            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                                Salvar Configurações de Email
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab: Upload -->
                <div id="config-upload" class="config-tab-content hidden">
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Configurações de Upload</h3>
                        <form id="config-upload-form" class="space-y-4">
                            <div class="grid md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tamanho Máximo de Arquivo (MB)</label>
                                    <input type="number" id="max_file_size" min="1" max="100" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <p class="text-xs text-gray-500 mt-1">Padrão: 5MB</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tamanho Máximo de Laudo (MB)</label>
                                    <input type="number" id="max_laudo_size" min="1" max="100" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <p class="text-xs text-gray-500 mt-1">Padrão: 5MB</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tamanho Máximo de Documento (MB)</label>
                                    <input type="number" id="max_documento_size" min="1" max="100" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <p class="text-xs text-gray-500 mt-1">Padrão: 10MB</p>
                                </div>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-blue-800 text-sm">
                                    <strong>ℹ️ Informação:</strong> Os limites do PHP são: upload_max_filesize = <?php echo ini_get('upload_max_filesize'); ?>, post_max_size = <?php echo ini_get('post_max_size'); ?>
                                </p>
                            </div>
                            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                                Salvar Configurações de Upload
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab: Segurança -->
                <div id="config-seguranca" class="config-tab-content hidden">
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Configurações de Segurança</h3>
                        <form id="config-seguranca-form" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tamanho Mínimo de Senha</label>
                                    <input type="number" id="password_min_length" min="6" max="32" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <p class="text-xs text-gray-500 mt-1">Padrão: 8 caracteres</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tentativas Máximas de Login</label>
                                    <input type="number" id="login_max_attempts" min="3" max="10" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <p class="text-xs text-gray-500 mt-1">Padrão: 5 tentativas</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tempo de Bloqueio (minutos)</label>
                                    <input type="number" id="login_lockout_time" min="5" max="60" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <p class="text-xs text-gray-500 mt-1">Padrão: 15 minutos</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tempo de Sessão (horas)</label>
                                    <input type="number" id="session_lifetime" min="1" max="168" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <p class="text-xs text-gray-500 mt-1">Padrão: 24 horas</p>
                                </div>
                            </div>
                            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                                Salvar Configurações de Segurança
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab: Sistema -->
                <div id="config-sistema" class="config-tab-content hidden">
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Informações do Sistema</h3>
                        <div id="system-info" class="space-y-4">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                            </div>
                        </div>
                        <div class="mt-6 flex space-x-4">
                            <button onclick="loadSystemInfo()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-sync-alt mr-2"></i>Atualizar Informações
                            </button>
                            <button onclick="clearCache()" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition">
                                <i class="fas fa-broom mr-2"></i>Limpar Cache e Logs
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Vagas Section -->
            <section id="vagas-section" class="hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Gerenciar Vagas</h2>
                    <button onclick="loadVagas()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-sync-alt mr-2"></i>Atualizar
                    </button>
                </div>

                <!-- Search and Filter -->
                <div class="bg-white p-4 rounded-xl shadow-md mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" id="job-search" placeholder="Buscar por título ou empresa..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <select id="job-status-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="todas">Todas</option>
                            <option value="ativa">Ativas</option>
                            <option value="pausada">Pausadas</option>
                            <option value="encerrada">Encerradas</option>
                        </select>
                        <button onclick="filterJobs()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                        <button onclick="exportJobs()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-download mr-2"></i>Exportar
                        </button>
                    </div>
                </div>

                <!-- Jobs Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vaga</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Localização</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Candidaturas</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visualizações</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="jobs-table-body" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
                                    <p class="mt-4">Carregando vagas...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-6">
                    <p class="text-sm text-gray-700">
                        Mostrando <span id="jobs-showing-start">0</span> a <span id="jobs-showing-end">0</span> de <span id="jobs-total">0</span> resultados
                    </p>
                    <div id="jobs-pagination" class="flex space-x-2">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </section>

            <!-- Relatórios Section -->
            <section id="relatorios-section" class="hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Relatórios e Analytics</h2>
                    <div class="flex space-x-3">
                        <button onclick="exportRelatorio('pdf')" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                        </button>
                        <button onclick="exportRelatorio('excel')" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                        </button>
                    </div>
                </div>

                <!-- Filtros de Período -->
                <div class="bg-white p-4 rounded-xl shadow-md mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                            <input type="date" id="relatorio-data-inicio" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                            <input type="date" id="relatorio-data-fim" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Relatório</label>
                            <select id="relatorio-tipo" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="geral">Geral</option>
                                <option value="vagas">Vagas</option>
                                <option value="candidaturas">Candidaturas</option>
                                <option value="empresas">Empresas</option>
                                <option value="usuarios">Usuários</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button onclick="gerarRelatorio()" class="w-full bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                                <i class="fas fa-chart-bar mr-2"></i>Gerar Relatório
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cards de Resumo -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Total de Vagas</p>
                                <p id="relatorio-total-vagas" class="text-3xl font-bold text-blue-600">0</p>
                            </div>
                            <i class="fas fa-briefcase text-blue-500 text-3xl"></i>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Candidaturas</p>
                                <p id="relatorio-total-candidaturas" class="text-3xl font-bold text-green-600">0</p>
                            </div>
                            <i class="fas fa-file-alt text-green-500 text-3xl"></i>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Taxa de Conversão</p>
                                <p id="relatorio-taxa-conversao" class="text-3xl font-bold text-purple-600">0%</p>
                            </div>
                            <i class="fas fa-percentage text-purple-500 text-3xl"></i>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Taxa de Aprovação</p>
                                <p id="relatorio-taxa-aprovacao" class="text-3xl font-bold text-orange-600">0%</p>
                            </div>
                            <i class="fas fa-check-circle text-orange-500 text-3xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Vagas por Status</h3>
                        <div id="grafico-vagas-status" class="h-64">
                            <div class="flex items-center justify-center h-full">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Candidaturas por Status</h3>
                        <div id="grafico-candidaturas-status" class="h-64">
                            <div class="flex items-center justify-center h-full">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatório de Planos -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-800">Relatório de Planos</h3>
                    </div>
                    <div id="relatorio-planos" class="p-6">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                        </div>
                    </div>
                </div>

                <!-- Tabela Detalhada -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-800">Detalhamento</h3>
                    </div>
                    <div id="relatorio-detalhes" class="p-6">
                        <p class="text-gray-500 text-center py-8">Selecione um período e tipo de relatório para visualizar os detalhes.</p>
                    </div>
                </div>
            </section>

            <!-- Gerenciar Planos Section -->
            <section id="gerenciar-planos-section" class="hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Gerenciar Planos das Empresas</h2>
                    <button onclick="loadEmpresasPlanos()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-sync-alt mr-2"></i>Atualizar
                    </button>
                </div>

                <!-- Search and Filter -->
                <div class="bg-white p-4 rounded-xl shadow-md mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" id="empresa-plano-search" placeholder="Buscar por nome ou CNPJ..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <select id="empresa-plano-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="todas">Todos os Planos</option>
                            <option value="com_plano">Com Plano Pago</option>
                            <option value="gratuito">Gratuito</option>
                            <option value="essencial">Essencial</option>
                            <option value="profissional">Profissional</option>
                            <option value="enterprise">Enterprise</option>
                        </select>
                        <select id="empresa-plano-status-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="todas">Todos os Status</option>
                            <option value="ativo">Ativo</option>
                            <option value="pendente">Pendente</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                        <button onclick="filterEmpresasPlanos()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </div>
                </div>

                <!-- Companies with Plans Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CNPJ</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plano Atual</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vagas</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Candidaturas</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="empresas-planos-table-body" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
                                    <p class="mt-4">Carregando empresas...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-6">
                    <p class="text-sm text-gray-700">
                        Mostrando <span id="empresas-planos-showing-start">0</span> a <span id="empresas-planos-showing-end">0</span> de <span id="empresas-planos-total">0</span> resultados
                    </p>
                    <div id="empresas-planos-pagination" class="flex space-x-2">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </section>

            <!-- Planos Pendentes Section -->
            <section id="planos-section" class="hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Solicitações de Planos Pendentes</h2>
                    <button onclick="loadPlanosPendentes()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-sync-alt mr-2"></i>Atualizar
                    </button>
                </div>

                <div id="planosPendentesList" class="space-y-4">
                    <div class="bg-white rounded-xl shadow-md p-6 text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
                        <p class="mt-4 text-gray-600">Carregando solicitações...</p>
                    </div>
                </div>
            </section>

            <!-- Companies Section -->
            <section id="empresas-section" class="hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Empresas Cadastradas</h2>
                    <button onclick="openCompanyModal()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-plus mr-2"></i>Adicionar Empresa
                    </button>
                </div>

                <!-- Search and Filter -->
                <div class="bg-white p-4 rounded-xl shadow-md mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" id="company-search" placeholder="Buscar por nome..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <select id="company-sector-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Setor</option>
                            <option value="Tecnologia">Tecnologia</option>
                            <option value="Saúde">Saúde</option>
                            <option value="Educação">Educação</option>
                            <option value="Varejo">Varejo</option>
                            <option value="Consultoria">Consultoria</option>
                            <option value="Marketing">Marketing</option>
                        </select>
                        <select id="company-status-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Status</option>
                            <option value="Ativa">Ativa</option>
                            <option value="Inativa">Inativa</option>
                            <option value="Pendente">Pendente</option>
                        </select>
                        <button onclick="filterCompanies()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </div>
                </div>

                <!-- Companies Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CNPJ</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Setor</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vagas Ativas</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Cadastro</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="companies-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-6">
                    <p class="text-sm text-gray-700">
                        Mostrando <span id="companies-showing-start">0</span> a <span id="companies-showing-end">0</span> de <span id="companies-total">0</span> resultados
                    </p>
                    <div id="companies-pagination" class="flex space-x-2">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- User Modal -->
    <div id="user-modal" class="modal">
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800" id="user-modal-title">Adicionar Usuário</h3>
                <button onclick="closeUserModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="user-form" onsubmit="saveUser(event)">
                <input type="hidden" id="user-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                        <input type="text" id="user-name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="user-email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CPF *</label>
                        <input type="text" id="user-cpf" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="000.000.000-00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                        <input type="tel" id="user-phone" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Deficiência *</label>
                        <select id="user-disability" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Selecione...</option>
                            <option value="Deficiência Física">Deficiência Física</option>
                            <option value="Deficiência Visual">Deficiência Visual</option>
                            <option value="Deficiência Auditiva">Deficiência Auditiva</option>
                            <option value="Deficiência Intelectual">Deficiência Intelectual</option>
                            <option value="Deficiência Múltipla">Deficiência Múltipla</option>
                        </select>
                    </div>
                    <div id="user-password-field">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Senha *</label>
                        <input type="password" id="user-password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" minlength="6">
                        <p class="text-xs text-gray-500 mt-1">Mínimo 6 caracteres</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select id="user-status" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                            <option value="pendente">Pendente</option>
                        </select>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="button" onclick="closeUserModal()" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Plano Modal -->
    <div id="edit-plano-modal" class="modal">
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Editar Plano da Empresa</h3>
                <button onclick="closeEditPlanoModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="edit-plano-form" onsubmit="savePlano(event)">
                <input type="hidden" id="edit-plano-company-id">
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Empresa:</p>
                        <p id="edit-plano-empresa-nome" class="font-semibold text-gray-900"></p>
                        <p id="edit-plano-empresa-cnpj" class="text-sm text-gray-500 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Plano *</label>
                        <select id="edit-plano-plano" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="gratuito">Gratuito</option>
                            <option value="essencial">Essencial - R$199/mês</option>
                            <option value="profissional">Profissional - R$399/mês</option>
                            <option value="enterprise">Enterprise - R$799/mês</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status do Plano *</label>
                        <select id="edit-plano-status" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="ativo">Ativo</option>
                            <option value="pendente">Pendente</option>
                            <option value="cancelado">Cancelado</option>
                            <option value="expirado">Expirado</option>
                        </select>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-yellow-800 text-sm">
                            <strong>⚠️ Atenção:</strong> Ao alterar o plano, a empresa será notificada automaticamente.
                        </p>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="button" onclick="closeEditPlanoModal()" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Company Modal -->
    <div id="company-modal" class="modal">
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800" id="company-modal-title">Adicionar Empresa</h3>
                <button onclick="closeCompanyModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="company-form" onsubmit="saveCompany(event)">
                <input type="hidden" id="company-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Razão Social *</label>
                        <input type="text" id="company-razao-social" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome Fantasia</label>
                        <input type="text" id="company-nome-fantasia" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CNPJ *</label>
                        <input type="text" id="company-cnpj" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="00.000.000/0000-00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Corporativo *</label>
                        <input type="email" id="company-email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                        <input type="tel" id="company-telefone" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Setor *</label>
                        <select id="company-sector" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Selecione...</option>
                            <option value="Tecnologia">Tecnologia</option>
                            <option value="Saúde">Saúde</option>
                            <option value="Educação">Educação</option>
                            <option value="Varejo">Varejo</option>
                            <option value="Consultoria">Consultoria</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Financeiro">Financeiro</option>
                            <option value="Indústria">Indústria</option>
                        </select>
                    </div>
                    <div id="company-password-field">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Senha *</label>
                        <input type="password" id="company-password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" minlength="6">
                        <p class="text-xs text-gray-500 mt-1">Mínimo 6 caracteres</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select id="company-status" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="ativa">Ativa</option>
                            <option value="inativa">Inativa</option>
                            <option value="pendente">Pendente</option>
                        </select>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="button" onclick="closeCompanyModal()" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/api.js"></script>
    <script>
        // Initialize data storage
        let users = [];
        let companies = [];
        let stats = null;
        let currentUserPage = 1;
        let currentCompanyPage = 1;
        const itemsPerPage = 10;

        // Navigation functionality
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                document.querySelectorAll('section').forEach(section => section.classList.add('hidden'));
                
                const target = this.getAttribute('href').substring(1);
                if (target === 'dashboard') {
                    document.getElementById('dashboard-section').classList.remove('hidden');
                    updateDashboard();
                } else if (target === 'usuarios') {
                    document.getElementById('usuarios-section').classList.remove('hidden');
                    loadUsers();
                } else if (target === 'empresas') {
                    document.getElementById('empresas-section').classList.remove('hidden');
                    loadCompanies();
                } else if (target === 'vagas') {
                    document.getElementById('vagas-section').classList.remove('hidden');
                    loadVagas();
                } else if (target === 'planos') {
                    document.getElementById('planos-section').classList.remove('hidden');
                    loadPlanosPendentes();
                } else if (target === 'gerenciar-planos') {
                    document.getElementById('gerenciar-planos-section').classList.remove('hidden');
                    loadEmpresasPlanos();
                } else if (target === 'relatorios') {
                    document.getElementById('relatorios-section').classList.remove('hidden');
                    loadRelatorios();
                } else if (target === 'configuracoes') {
                    document.getElementById('configuracoes-section').classList.remove('hidden');
                    loadSettings();
                }
            });
        });

        // Load statistics from API
        async function loadStats() {
            try {
                const response = await obterEstatisticas('admin');
                if (response.success && response.data) {
                    stats = response.data;
                    updateDashboard();
                }
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        }

        // Update dashboard statistics
        function updateDashboard() {
            if (!stats) return;
            
            // Main stats
            document.getElementById('total-users').textContent = formatNumber(stats.total_usuarios_pcd || 0);
            document.getElementById('total-companies').textContent = formatNumber(stats.total_empresas || 0);
            document.getElementById('total-jobs').textContent = formatNumber(stats.vagas_ativas || stats.total_vagas || 0);
            document.getElementById('total-applications').textContent = formatNumber(stats.total_candidaturas || 0);
            
            // Secondary stats
            document.getElementById('conversion-rate').textContent = (stats.taxa_conversao || 0).toFixed(1) + '%';
            document.getElementById('approval-rate').textContent = (stats.taxa_aprovacao || 0).toFixed(1) + '%';
            document.getElementById('approved-applications').textContent = formatNumber(stats.candidaturas_aprovadas || 0);
            
            // Growth indicators
            updateGrowthIndicator('users-growth', stats.usuarios_por_mes);
            updateGrowthIndicator('companies-growth', stats.empresas_por_mes);
            updateGrowthIndicator('applications-growth', stats.candidaturas_por_mes);
            
            // Jobs status
            const jobsStatus = stats.vagas_por_status || {};
            const activeJobs = jobsStatus.ativa || 0;
            const pausedJobs = jobsStatus.pausada || 0;
            document.getElementById('jobs-status').innerHTML = `
                <span class="text-green-200">${activeJobs} ativas</span> | 
                <span class="text-yellow-200">${pausedJobs} pausadas</span>
            `;
            
            // Growth chart
            renderGrowthChart();
            
            // Status distribution
            renderStatusDistribution();
            
            // Recent registrations
            renderRecentRegistrations();
            
            // Popular jobs
            renderPopularJobs();
        }
        
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        function updateGrowthIndicator(elementId, monthlyData) {
            if (!monthlyData || monthlyData.length < 2) {
                document.getElementById(elementId).innerHTML = '<i class="fas fa-minus"></i> <span>Sem dados suficientes</span>';
                return;
            }
            
            const current = monthlyData[monthlyData.length - 1]?.total || 0;
            const previous = monthlyData[monthlyData.length - 2]?.total || 0;
            
            if (previous === 0) {
                document.getElementById(elementId).innerHTML = '<i class="fas fa-arrow-up"></i> <span>Novo</span>';
                return;
            }
            
            const growth = ((current - previous) / previous * 100).toFixed(1);
            const isPositive = growth >= 0;
            const icon = isPositive ? 'fa-arrow-up' : 'fa-arrow-down';
            const color = isPositive ? 'text-green-200' : 'text-red-200';
            
            document.getElementById(elementId).innerHTML = `
                <i class="fas ${icon} ${color}"></i> 
                <span class="${color}">${isPositive ? '+' : ''}${growth}% este mês</span>
            `;
        }
        
        function renderGrowthChart() {
            const chartDiv = document.getElementById('growth-chart');
            if (!stats.usuarios_por_mes || !stats.empresas_por_mes) {
                chartDiv.innerHTML = '<p class="text-gray-500 text-center w-full">Sem dados suficientes</p>';
                return;
            }
            
            // Combinar meses únicos
            const months = new Set();
            [...stats.usuarios_por_mes, ...stats.empresas_por_mes].forEach(item => {
                if (item.mes) months.add(item.mes);
            });
            const sortedMonths = Array.from(months).sort();
            
            if (sortedMonths.length === 0) {
                chartDiv.innerHTML = '<p class="text-gray-500 text-center w-full">Sem dados</p>';
                return;
            }
            
            // Encontrar valores máximos para escala
            const maxUsers = Math.max(...stats.usuarios_por_mes.map(u => u.total || 0), 1);
            const maxCompanies = Math.max(...stats.empresas_por_mes.map(e => e.total || 0), 1);
            const maxValue = Math.max(maxUsers, maxCompanies, 1);
            
            let html = '';
            sortedMonths.forEach(month => {
                const userData = stats.usuarios_por_mes.find(u => u.mes === month);
                const companyData = stats.empresas_por_mes.find(e => e.mes === month);
                const userValue = userData?.total || 0;
                const companyValue = companyData?.total || 0;
                
                const userHeight = (userValue / maxValue) * 100;
                const companyHeight = (companyValue / maxValue) * 100;
                
                const monthLabel = new Date(month + '-01').toLocaleDateString('pt-BR', { month: 'short' });
                
                html += `
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full flex items-end justify-center space-x-1 mb-2" style="height: 200px;">
                            <div class="w-full bg-purple-400 rounded-t" style="height: ${userHeight}%" title="Usuários: ${userValue}"></div>
                            <div class="w-full bg-blue-400 rounded-t" style="height: ${companyHeight}%" title="Empresas: ${companyValue}"></div>
                        </div>
                        <span class="text-xs text-gray-600 font-medium">${monthLabel}</span>
                    </div>
                `;
            });
            
            chartDiv.innerHTML = html || '<p class="text-gray-500 text-center w-full">Sem dados</p>';
        }
        
        function renderStatusDistribution() {
            const distDiv = document.getElementById('status-distribution');
            
            if (!stats.usuarios_por_status && !stats.empresas_por_status && !stats.vagas_por_status) {
                distDiv.innerHTML = '<p class="text-gray-500 text-center py-4">Sem dados</p>';
                return;
            }
            
            let html = '';
            
            // Usuários por status
            if (stats.usuarios_por_status) {
                html += '<div class="mb-4"><h4 class="text-sm font-semibold text-gray-700 mb-2">Usuários PCD</h4>';
                Object.entries(stats.usuarios_por_status).forEach(([status, total]) => {
                    const statusText = status === 'ativo' ? 'Ativos' : status === 'pendente' ? 'Pendentes' : 'Inativos';
                    const color = status === 'ativo' ? 'bg-green-500' : status === 'pendente' ? 'bg-yellow-500' : 'bg-red-500';
                    const percentage = stats.total_usuarios_pcd > 0 ? (total / stats.total_usuarios_pcd * 100).toFixed(1) : 0;
                    html += `
                        <div class="mb-2">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700">${statusText}</span>
                                <span class="text-gray-600">${total} (${percentage}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="${color} h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            // Empresas por status
            if (stats.empresas_por_status) {
                html += '<div class="mb-4"><h4 class="text-sm font-semibold text-gray-700 mb-2">Empresas</h4>';
                Object.entries(stats.empresas_por_status).forEach(([status, total]) => {
                    const statusText = status === 'ativa' ? 'Ativas' : status === 'pendente' ? 'Pendentes' : 'Inativas';
                    const color = status === 'ativa' ? 'bg-green-500' : status === 'pendente' ? 'bg-yellow-500' : 'bg-red-500';
                    const percentage = stats.total_empresas > 0 ? (total / stats.total_empresas * 100).toFixed(1) : 0;
                    html += `
                        <div class="mb-2">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700">${statusText}</span>
                                <span class="text-gray-600">${total} (${percentage}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="${color} h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            distDiv.innerHTML = html || '<p class="text-gray-500 text-center py-4">Sem dados</p>';
        }
        
        function renderRecentRegistrations() {
            const recentDiv = document.getElementById('recent-registrations');
            
            if (!stats.cadastros_recentes || stats.cadastros_recentes.length === 0) {
                recentDiv.innerHTML = '<p class="text-gray-500 text-center py-4">Nenhum registro recente.</p>';
                return;
            }
            
            let html = '';
            stats.cadastros_recentes.forEach(item => {
                const date = item.created_at ? new Date(item.created_at).toLocaleDateString('pt-BR') : '-';
                const time = item.created_at ? new Date(item.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }) : '';
                const isUser = item.tipo === 'user';
                const icon = isUser ? 'fa-user' : 'fa-building';
                const bgColor = isUser ? 'bg-purple-100' : 'bg-blue-100';
                const iconColor = isUser ? 'text-purple-600' : 'text-blue-600';
                const typeText = isUser ? 'Usuário PCD' : 'Empresa';
                
                html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-3">
                            <div class="${bgColor} p-2 rounded-full">
                                <i class="fas ${icon} ${iconColor}"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">${item.nome || 'Sem nome'}</p>
                                <p class="text-sm text-gray-500">${typeText}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-500 block">${date}</span>
                            <span class="text-xs text-gray-400">${time}</span>
                        </div>
                    </div>
                `;
            });
            
            recentDiv.innerHTML = html;
        }
        
        function renderPopularJobs() {
            const jobsDiv = document.getElementById('popular-jobs');
            
            if (!stats.vagas_mais_populares || stats.vagas_mais_populares.length === 0) {
                jobsDiv.innerHTML = '<p class="text-gray-500 text-center py-4">Nenhuma vaga encontrada.</p>';
                return;
            }
            
            let html = '';
            stats.vagas_mais_populares.forEach((job, index) => {
                const candidates = job.total_candidatos || 0;
                const views = job.visualizacoes || 0;
                
                html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="bg-purple-100 text-purple-600 text-xs font-bold px-2 py-1 rounded">#${index + 1}</span>
                                <p class="font-medium text-gray-800">${job.titulo || 'Sem título'}</p>
                            </div>
                            <p class="text-sm text-gray-500">${job.nome_fantasia || 'Empresa'}</p>
                            <div class="flex items-center space-x-3 mt-1">
                                <span class="text-xs text-gray-400">
                                    <i class="fas fa-eye"></i> ${views} visualizações
                                </span>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium ml-4">
                            ${candidates} candidatos
                        </span>
                    </div>
                `;
            });
            
            jobsDiv.innerHTML = html;
        }
        
        async function refreshDashboard() {
            await loadStats();
        }

        // Load users from API
        async function loadUsers(status = 'todas') {
            try {
                const response = await adminListarUsuarios({ status, page: currentUserPage, limit: itemsPerPage });
                if (response.success && response.data) {
                    // A API retorna data como array diretamente
                    users = Array.isArray(response.data) ? response.data : [];
                    renderUsers();
                } else {
                    console.error('Erro ao carregar usuários:', response.error);
                    document.getElementById('users-table-body').innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">Erro ao carregar usuários.</td></tr>';
                }
            } catch (error) {
                console.error('Erro ao carregar usuários:', error);
                document.getElementById('users-table-body').innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">Erro ao carregar usuários.</td></tr>';
            }
        }

        // User Management Functions
        function renderUsers() {
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = '';
            
            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">Nenhum usuário encontrado.</td></tr>';
                return;
            }
            
            users.forEach(user => {
                const name = user.nome || 'Sem nome';
                const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                const status = user.status || 'pendente';
                const statusText = status === 'ativo' ? 'Ativo' : status === 'pendente' ? 'Pendente' : 'Inativo';
                const statusClass = status === 'ativo' ? 'bg-green-100 text-green-800' : 
                                   status === 'pendente' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-red-100 text-red-800';
                const date = user.created_at ? new Date(user.created_at).toLocaleDateString('pt-BR') : '-';
                const disability = user.tipo_deficiencia || '-';
                
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <span class="text-purple-600 font-medium">${initials}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${name}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.email || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${disability}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${date}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button onclick="viewUser(${user.id})" class="text-blue-600 hover:text-blue-900" title="Ver"><i class="fas fa-eye"></i></button>
                            <button onclick="editUser(${user.id})" class="text-green-600 hover:text-green-900" title="Editar"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900" title="Excluir"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
            
            // Update pagination info
            document.getElementById('users-showing-start').textContent = (currentUserPage - 1) * itemsPerPage + 1;
            document.getElementById('users-showing-end').textContent = Math.min(currentUserPage * itemsPerPage, users.length);
            document.getElementById('users-total').textContent = users.length;
        }

        function renderUserPagination(totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            const pagination = document.getElementById('users-pagination');
            pagination.innerHTML = '';
            
            // Previous button
            pagination.innerHTML += `
                <button onclick="changeUserPage(${currentUserPage - 1})" 
                        ${currentUserPage === 1 ? 'disabled' : ''} 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Anterior
                </button>
            `;
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentUserPage - 1 && i <= currentUserPage + 1)) {
                    const activeClass = i === currentUserPage ? 'bg-purple-600 text-white' : 'border border-gray-300 hover:bg-gray-50';
                    pagination.innerHTML += `
                        <button onclick="changeUserPage(${i})" class="px-4 py-2 ${activeClass} rounded-lg">
                            ${i}
                        </button>
                    `;
                } else if (i === currentUserPage - 2 || i === currentUserPage + 2) {
                    pagination.innerHTML += '<span class="px-2">...</span>';
                }
            }
            
            // Next button
            pagination.innerHTML += `
                <button onclick="changeUserPage(${currentUserPage + 1})" 
                        ${currentUserPage === totalPages ? 'disabled' : ''} 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Próximo
                </button>
            `;
        }

        async function changeUserPage(page) {
            currentUserPage = page;
            const statusFilter = document.getElementById('user-status-filter')?.value || 'todas';
            await loadUsers(statusFilter === 'Todas' ? 'todas' : statusFilter.toLowerCase());
        }

        async function filterUsers() {
            const statusFilter = document.getElementById('user-status-filter').value;
            currentUserPage = 1;
            await loadUsers(statusFilter === 'Todas' ? 'todas' : statusFilter.toLowerCase());
        }

        // Add real-time search - recarregar da API
        document.getElementById('user-search')?.addEventListener('input', function() {
            // Por enquanto, apenas recarregar - pode implementar busca no backend depois
            const statusFilter = document.getElementById('user-status-filter').value;
            loadUsers(statusFilter === 'Todas' ? 'todas' : statusFilter.toLowerCase());
        });
        
        document.getElementById('user-status-filter')?.addEventListener('change', filterUsers);

        function openUserModal(userId = null) {
            const modal = document.getElementById('user-modal');
            const title = document.getElementById('user-modal-title');
            const passwordField = document.getElementById('user-password-field');
            const passwordInput = document.getElementById('user-password');
            
            if (userId) {
                const user = users.find(u => u.id === userId);
                if (user) {
                    title.textContent = 'Editar Usuário';
                    document.getElementById('user-id').value = user.id;
                    document.getElementById('user-name').value = user.nome || user.name || '';
                    document.getElementById('user-email').value = user.email || '';
                    document.getElementById('user-cpf').value = user.cpf || '';
                    document.getElementById('user-phone').value = user.telefone || user.phone || '';
                    document.getElementById('user-disability').value = user.tipo_deficiencia || user.disability || '';
                    document.getElementById('user-status').value = user.status || 'pendente';
                    passwordField.style.display = 'none';
                    passwordInput.removeAttribute('required');
                }
            } else {
                title.textContent = 'Adicionar Usuário';
                document.getElementById('user-form').reset();
                document.getElementById('user-id').value = '';
                passwordField.style.display = 'block';
                passwordInput.setAttribute('required', 'required');
            }
            
            modal.classList.add('active');
        }

        function closeUserModal() {
            document.getElementById('user-modal').classList.remove('active');
            document.getElementById('user-form').reset();
        }

        async function saveUser(event) {
            event.preventDefault();
            
            const userId = document.getElementById('user-id').value;
            
            if (userId) {
                // Atualizar status via API
                const status = document.getElementById('user-status').value;
                try {
                    const response = await adminAtualizarStatusUsuario(parseInt(userId), status);
                    if (response.success) {
                        alert('Status do usuário atualizado com sucesso!');
                        closeUserModal();
                        await loadUsers();
                        await loadStats();
                    } else {
                        alert('Erro ao atualizar usuário: ' + (response.error || 'Erro desconhecido'));
                    }
                } catch (error) {
                    console.error('Erro ao salvar usuário:', error);
                    alert('Erro ao atualizar usuário. Tente novamente.');
                }
            } else {
                // Criar novo usuário
                const formData = new FormData();
                formData.append('nome', document.getElementById('user-name').value);
                formData.append('email', document.getElementById('user-email').value);
                formData.append('cpf', document.getElementById('user-cpf').value);
                formData.append('telefone', document.getElementById('user-phone').value || '');
                formData.append('tipo_deficiencia', document.getElementById('user-disability').value);
                formData.append('status', document.getElementById('user-status').value);
                formData.append('senha', document.getElementById('user-password').value);
                
                try {
                    const response = await fetch('api/admin_usuarios.php?action=create', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Usuário criado com sucesso!');
                        closeUserModal();
                        await loadUsers();
                        await loadStats();
                    } else {
                        alert('Erro ao criar usuário: ' + (result.error || 'Erro desconhecido'));
                    }
                } catch (error) {
                    console.error('Erro ao criar usuário:', error);
                    alert('Erro ao criar usuário. Tente novamente.');
                }
            }
        }

        function editUser(userId) {
            openUserModal(userId);
        }

        function viewUser(userId) {
            const user = users.find(u => u.id === userId);
            if (user) {
                const name = user.nome || user.name || 'Sem nome';
                const email = user.email || '-';
                const phone = user.telefone || user.phone || '-';
                const disability = user.tipo_deficiencia || user.disability || '-';
                const status = user.status || 'pendente';
                const date = user.created_at ? new Date(user.created_at).toLocaleDateString('pt-BR') : '-';
                alert(`Nome: ${name}\nEmail: ${email}\nTelefone: ${phone}\nDeficiência: ${disability}\nStatus: ${status}\nData: ${date}`);
            }
        }

        async function deleteUser(userId) {
            if (!confirm('Tem certeza que deseja excluir este usuário?')) {
                return;
            }
            
            // Por enquanto, apenas desativar - pode implementar exclusão depois
            try {
                const response = await adminAtualizarStatusUsuario(userId, 'inativo');
                if (response.success) {
                    alert('Usuário desativado com sucesso!');
                    await loadUsers();
                    await loadStats();
                } else {
                    alert('Erro ao desativar usuário: ' + (response.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao excluir usuário:', error);
                alert('Erro ao desativar usuário. Tente novamente.');
            }
        }

        // Load companies from API
        async function loadCompanies(status = 'todas') {
            try {
                const response = await adminListarEmpresas({ status, page: currentCompanyPage, limit: itemsPerPage });
                if (response.success && response.data) {
                    // A API retorna data como array diretamente
                    companies = Array.isArray(response.data) ? response.data : [];
                    renderCompanies();
                } else {
                    console.error('Erro ao carregar empresas:', response.error);
                    document.getElementById('companies-table-body').innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-500">Erro ao carregar empresas.</td></tr>';
                }
            } catch (error) {
                console.error('Erro ao carregar empresas:', error);
                document.getElementById('companies-table-body').innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-500">Erro ao carregar empresas.</td></tr>';
            }
        }

        // Company Management Functions
        function renderCompanies() {
            const tbody = document.getElementById('companies-table-body');
            tbody.innerHTML = '';
            
            if (companies.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-500">Nenhuma empresa encontrada.</td></tr>';
                return;
            }
            
            companies.forEach(company => {
                const name = company.nome_fantasia || company.razao_social || 'Sem nome';
                const email = company.email_corporativo || '-';
                const cnpj = company.cnpj || '-';
                const sector = company.setor || '-';
                const jobs = company.total_vagas || 0;
                const status = company.status || 'pendente';
                const statusText = status === 'ativa' ? 'Ativa' : status === 'pendente' ? 'Pendente' : 'Inativa';
                const statusClass = status === 'ativa' ? 'bg-green-100 text-green-800' : 
                                   status === 'pendente' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-red-100 text-red-800';
                const date = company.created_at ? new Date(company.created_at).toLocaleDateString('pt-BR') : '-';
                
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-building text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${name}</div>
                                    <div class="text-sm text-gray-500">${email}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${cnpj}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${sector}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${jobs}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${date}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button onclick="viewCompany(${company.id})" class="text-blue-600 hover:text-blue-900" title="Ver"><i class="fas fa-eye"></i></button>
                            <button onclick="editCompany(${company.id})" class="text-green-600 hover:text-green-900" title="Editar"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteCompany(${company.id})" class="text-red-600 hover:text-red-900" title="Excluir"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
            
            document.getElementById('companies-showing-start').textContent = (currentCompanyPage - 1) * itemsPerPage + 1;
            document.getElementById('companies-showing-end').textContent = Math.min(currentCompanyPage * itemsPerPage, companies.length);
            document.getElementById('companies-total').textContent = companies.length;
            
            renderCompanyPagination(companies.length);
        }

        function renderCompanyPagination(totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            const pagination = document.getElementById('companies-pagination');
            pagination.innerHTML = '';
            
            pagination.innerHTML += `
                <button onclick="changeCompanyPage(${currentCompanyPage - 1})" 
                        ${currentCompanyPage === 1 ? 'disabled' : ''} 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Anterior
                </button>
            `;
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentCompanyPage - 1 && i <= currentCompanyPage + 1)) {
                    const activeClass = i === currentCompanyPage ? 'bg-purple-600 text-white' : 'border border-gray-300 hover:bg-gray-50';
                    pagination.innerHTML += `
                        <button onclick="changeCompanyPage(${i})" class="px-4 py-2 ${activeClass} rounded-lg">
                            ${i}
                        </button>
                    `;
                } else if (i === currentCompanyPage - 2 || i === currentCompanyPage + 2) {
                    pagination.innerHTML += '<span class="px-2">...</span>';
                }
            }
            
            pagination.innerHTML += `
                <button onclick="changeCompanyPage(${currentCompanyPage + 1})" 
                        ${currentCompanyPage === totalPages ? 'disabled' : ''} 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Próximo
                </button>
            `;
        }

        async function changeCompanyPage(page) {
            currentCompanyPage = page;
            const statusFilter = document.getElementById('company-status-filter')?.value || 'todas';
            await loadCompanies(statusFilter === 'Todas' ? 'todas' : statusFilter.toLowerCase());
        }

        async function filterCompanies() {
            const statusFilter = document.getElementById('company-status-filter').value;
            currentCompanyPage = 1;
            await loadCompanies(statusFilter === 'Todas' ? 'todas' : statusFilter.toLowerCase());
        }

        document.getElementById('company-search')?.addEventListener('input', function() {
            // Por enquanto, apenas recarregar - pode implementar busca no backend depois
            const statusFilter = document.getElementById('company-status-filter').value;
            loadCompanies(statusFilter === 'Todas' ? 'todas' : statusFilter.toLowerCase());
        });
        
        document.getElementById('company-status-filter')?.addEventListener('change', filterCompanies);

        function openCompanyModal(companyId = null) {
            const modal = document.getElementById('company-modal');
            const title = document.getElementById('company-modal-title');
            const passwordField = document.getElementById('company-password-field');
            const passwordInput = document.getElementById('company-password');
            
            if (companyId) {
                const company = companies.find(c => c.id === companyId);
                if (company) {
                    title.textContent = 'Editar Empresa';
                    document.getElementById('company-id').value = company.id;
                    const razaoSocial = document.getElementById('company-razao-social');
                    const nomeFantasia = document.getElementById('company-nome-fantasia');
                    if (razaoSocial) razaoSocial.value = company.razao_social || '';
                    if (nomeFantasia) nomeFantasia.value = company.nome_fantasia || '';
                    document.getElementById('company-cnpj').value = company.cnpj || '';
                    document.getElementById('company-email').value = company.email_corporativo || company.email || '';
                    const telefoneField = document.getElementById('company-telefone');
                    if (telefoneField) telefoneField.value = company.telefone_empresa || company.telefone || '';
                    document.getElementById('company-sector').value = company.setor || company.sector || '';
                    document.getElementById('company-status').value = company.status || 'pendente';
                    if (passwordField) passwordField.style.display = 'none';
                    if (passwordInput) passwordInput.removeAttribute('required');
                }
            } else {
                title.textContent = 'Adicionar Empresa';
                document.getElementById('company-form').reset();
                document.getElementById('company-id').value = '';
                if (passwordField) passwordField.style.display = 'block';
                if (passwordInput) passwordInput.setAttribute('required', 'required');
            }
            
            modal.classList.add('active');
        }

        function closeCompanyModal() {
            document.getElementById('company-modal').classList.remove('active');
            document.getElementById('company-form').reset();
        }

        async function saveCompany(event) {
            event.preventDefault();
            
            const companyId = document.getElementById('company-id').value;
            
            if (companyId) {
                // Atualizar status via API
                const status = document.getElementById('company-status').value;
                try {
                    const response = await adminAtualizarStatusEmpresa(parseInt(companyId), status);
                    if (response.success) {
                        alert('Status da empresa atualizado com sucesso!');
                        closeCompanyModal();
                        await loadCompanies();
                        await loadStats();
                    } else {
                        alert('Erro ao atualizar empresa: ' + (response.error || 'Erro desconhecido'));
                    }
                } catch (error) {
                    console.error('Erro ao salvar empresa:', error);
                    alert('Erro ao atualizar empresa. Tente novamente.');
                }
            } else {
                // Criar nova empresa
                const formData = new FormData();
                formData.append('razao_social', document.getElementById('company-razao-social').value);
                formData.append('nome_fantasia', document.getElementById('company-nome-fantasia').value || '');
                formData.append('cnpj', document.getElementById('company-cnpj').value);
                formData.append('email_corporativo', document.getElementById('company-email').value);
                formData.append('telefone', document.getElementById('company-telefone').value || '');
                formData.append('setor', document.getElementById('company-sector').value);
                formData.append('status', document.getElementById('company-status').value);
                formData.append('senha', document.getElementById('company-password').value);
                
                try {
                    const response = await fetch('api/admin_empresas.php?action=create', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Empresa criada com sucesso!');
                        closeCompanyModal();
                        await loadCompanies();
                        await loadStats();
                    } else {
                        const errorMsg = result.error || 'Erro desconhecido';
                        const details = result.details ? '\n\nDetalhes: ' + result.details : '';
                        alert('Erro ao criar empresa: ' + errorMsg + details);
                        console.error('Erro completo:', result);
                    }
                } catch (error) {
                    console.error('Erro ao criar empresa:', error);
                    alert('Erro ao criar empresa. Tente novamente.');
                }
            }
        }

        function editCompany(companyId) {
            openCompanyModal(companyId);
        }

        function viewCompany(companyId) {
            const company = companies.find(c => c.id === companyId);
            if (company) {
                const name = company.nome_fantasia || company.razao_social || company.name || 'Sem nome';
                const cnpj = company.cnpj || '-';
                const email = company.email_corporativo || company.email || '-';
                const sector = company.setor || company.sector || '-';
                const jobs = company.total_vagas || company.jobs || 0;
                const status = company.status || 'pendente';
                const date = company.created_at ? new Date(company.created_at).toLocaleDateString('pt-BR') : '-';
                alert(`Nome: ${name}\nCNPJ: ${cnpj}\nEmail: ${email}\nSetor: ${sector}\nVagas: ${jobs}\nStatus: ${status}\nData: ${date}`);
            }
        }

        async function deleteCompany(companyId) {
            if (!confirm('Tem certeza que deseja excluir esta empresa?')) {
                return;
            }
            
            // Por enquanto, apenas desativar - pode implementar exclusão depois
            try {
                const response = await adminAtualizarStatusEmpresa(companyId, 'inativa');
                if (response.success) {
                    alert('Empresa desativada com sucesso!');
                    await loadCompanies();
                    await loadStats();
                } else {
                    alert('Erro ao desativar empresa: ' + (response.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao excluir empresa:', error);
                alert('Erro ao desativar empresa. Tente novamente.');
            }
        }

        // Configurações Functions
        let currentSettings = null;

        function showConfigTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.config-tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.querySelectorAll('.config-tab').forEach(btn => {
                btn.classList.remove('active', 'border-purple-600', 'text-purple-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab
            document.getElementById(`config-${tab}`).classList.remove('hidden');
            const tabBtn = document.getElementById(`tab-${tab}`);
            tabBtn.classList.add('active', 'border-purple-600', 'text-purple-600');
            tabBtn.classList.remove('border-transparent', 'text-gray-500');
            
            if (tab === 'sistema') {
                loadSystemInfo();
            }
        }

        async function loadSettings() {
            try {
                const result = await obterConfiguracoes();
                
                if (result.success && result.data) {
                    currentSettings = result.data;
                    populateSettingsForms(result.data);
                }
            } catch (error) {
                console.error('Erro ao carregar configurações:', error);
            }
        }

        function populateSettingsForms(settings) {
            // Geral
            document.getElementById('site_name').value = settings.site_name || '';
            document.getElementById('site_description').value = settings.site_description || '';
            document.getElementById('base_url').value = settings.base_url || '';
            document.getElementById('timezone').value = settings.timezone || 'America/Sao_Paulo';
            
            // Email
            if (settings.email) {
                document.getElementById('smtp_host').value = settings.email.smtp_host || '';
                document.getElementById('smtp_port').value = settings.email.smtp_port || 587;
                document.getElementById('smtp_user').value = settings.email.smtp_user || '';
                document.getElementById('smtp_pass').value = settings.email.smtp_pass || '';
                document.getElementById('from_email').value = settings.email.from_email || '';
                document.getElementById('from_name').value = settings.email.from_name || 'Vigged';
            }
            
            // Upload
            if (settings.upload) {
                document.getElementById('max_file_size').value = Math.round((settings.upload.max_file_size || 5242880) / 1024 / 1024);
                document.getElementById('max_laudo_size').value = Math.round((settings.upload.max_laudo_size || 5242880) / 1024 / 1024);
                document.getElementById('max_documento_size').value = Math.round((settings.upload.max_documento_size || 10485760) / 1024 / 1024);
            }
            
            // Segurança
            if (settings.security) {
                document.getElementById('password_min_length').value = settings.security.password_min_length || 8;
                document.getElementById('login_max_attempts').value = settings.security.login_max_attempts || 5;
                document.getElementById('login_lockout_time').value = Math.round((settings.security.login_lockout_time || 900) / 60);
                document.getElementById('session_lifetime').value = Math.round((settings.security.session_lifetime || 86400) / 3600);
            }
        }

        // Form handlers
        document.getElementById('config-geral-form')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            await saveSettings('geral');
        });

        document.getElementById('config-email-form')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            await saveSettings('email');
        });

        document.getElementById('config-upload-form')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            await saveSettings('upload');
        });

        document.getElementById('config-seguranca-form')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            await saveSettings('seguranca');
        });

        async function saveSettings(section) {
            if (!currentSettings) {
                currentSettings = {};
            }
            
            let dataToSave = { ...currentSettings };
            
            if (section === 'geral') {
                dataToSave.site_name = document.getElementById('site_name').value;
                dataToSave.site_description = document.getElementById('site_description').value;
                dataToSave.base_url = document.getElementById('base_url').value;
                dataToSave.timezone = document.getElementById('timezone').value;
            } else if (section === 'email') {
                if (!dataToSave.email) dataToSave.email = {};
                dataToSave.email.smtp_host = document.getElementById('smtp_host').value;
                dataToSave.email.smtp_port = parseInt(document.getElementById('smtp_port').value);
                dataToSave.email.smtp_user = document.getElementById('smtp_user').value;
                dataToSave.email.smtp_pass = document.getElementById('smtp_pass').value;
                dataToSave.email.from_email = document.getElementById('from_email').value;
                dataToSave.email.from_name = document.getElementById('from_name').value;
            } else if (section === 'upload') {
                if (!dataToSave.upload) dataToSave.upload = {};
                dataToSave.upload.max_file_size = parseInt(document.getElementById('max_file_size').value) * 1024 * 1024;
                dataToSave.upload.max_laudo_size = parseInt(document.getElementById('max_laudo_size').value) * 1024 * 1024;
                dataToSave.upload.max_documento_size = parseInt(document.getElementById('max_documento_size').value) * 1024 * 1024;
            } else if (section === 'seguranca') {
                if (!dataToSave.security) dataToSave.security = {};
                dataToSave.security.password_min_length = parseInt(document.getElementById('password_min_length').value);
                dataToSave.security.login_max_attempts = parseInt(document.getElementById('login_max_attempts').value);
                dataToSave.security.login_lockout_time = parseInt(document.getElementById('login_lockout_time').value) * 60;
                dataToSave.security.session_lifetime = parseInt(document.getElementById('session_lifetime').value) * 3600;
            }
            
            try {
                const result = await salvarConfiguracoes(dataToSave);
                
                if (result.success) {
                    alert('Configurações salvas com sucesso!');
                    currentSettings = dataToSave;
                } else {
                    alert('Erro ao salvar configurações: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao salvar configurações:', error);
                alert('Erro ao salvar configurações. Tente novamente.');
            }
        }

        async function loadSystemInfo() {
            const infoDiv = document.getElementById('system-info');
            infoDiv.innerHTML = '<div class="flex items-center justify-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div></div>';
            
            try {
                const result = await obterInfoSistema();
                
                if (result.success && result.data) {
                    const info = result.data;
                    let html = `
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-bold text-gray-800 mb-3">PHP</h4>
                                <ul class="space-y-2 text-sm">
                                    <li><strong>Versão:</strong> ${info.php_version}</li>
                                    <li><strong>Servidor:</strong> ${info.server_software}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-3">Extensões PHP</h4>
                                <ul class="space-y-2 text-sm">
                                    ${Object.entries(info.php_extensions).map(([ext, loaded]) => 
                                        `<li><strong>${ext}:</strong> <span class="${loaded ? 'text-green-600' : 'text-red-600'}">${loaded ? '✓ Instalada' : '✗ Não instalada'}</span></li>`
                                    ).join('')}
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-3">Configurações PHP</h4>
                                <ul class="space-y-2 text-sm">
                                    <li><strong>upload_max_filesize:</strong> ${info.php_settings.upload_max_filesize}</li>
                                    <li><strong>post_max_size:</strong> ${info.php_settings.post_max_size}</li>
                                    <li><strong>max_execution_time:</strong> ${info.php_settings.max_execution_time}s</li>
                                    <li><strong>memory_limit:</strong> ${info.php_settings.memory_limit}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-3">Banco de Dados</h4>
                                <ul class="space-y-2 text-sm">
                                    <li><strong>Status:</strong> <span class="${info.database.connected ? 'text-green-600' : 'text-red-600'}">${info.database.connected ? '✓ Conectado' : '✗ Desconectado'}</span></li>
                                    ${info.database.version ? `<li><strong>Versão:</strong> ${info.database.version}</li>` : ''}
                                    ${info.database.error ? `<li class="text-red-600"><strong>Erro:</strong> ${info.database.error}</li>` : ''}
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-3">Diretórios</h4>
                                <ul class="space-y-2 text-sm">
                                    <li><strong>config/:</strong> <span class="${info.directories.config_writable ? 'text-green-600' : 'text-red-600'}">${info.directories.config_writable ? '✓ Gravável' : '✗ Não gravável'}</span></li>
                                    <li><strong>uploads/:</strong> <span class="${info.directories.uploads_writable ? 'text-green-600' : 'text-red-600'}">${info.directories.uploads_writable ? '✓ Gravável' : '✗ Não gravável'}</span></li>
                                    <li><strong>Tamanho uploads/:</strong> ${info.directories.uploads_size}</li>
                                </ul>
                            </div>
                        </div>
                    `;
                    infoDiv.innerHTML = html;
                }
            } catch (error) {
                console.error('Erro ao carregar informações do sistema:', error);
                infoDiv.innerHTML = '<p class="text-red-600">Erro ao carregar informações do sistema.</p>';
            }
        }

        async function clearCache() {
            if (!confirm('Tem certeza que deseja limpar o cache e logs antigos?')) {
                return;
            }
            
            try {
                const result = await limparCache();
                
                if (result.success) {
                    alert('Cache limpo com sucesso!\n\n' + (result.cleared || []).join('\n'));
                } else {
                    alert('Erro ao limpar cache: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao limpar cache:', error);
                alert('Erro ao limpar cache. Tente novamente.');
            }
        }

        // Carregar planos pendentes
        async function loadPlanosPendentes() {
            const listDiv = document.getElementById('planosPendentesList');
            listDiv.innerHTML = '<div class="bg-white rounded-xl shadow-md p-6 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div><p class="mt-4 text-gray-600">Carregando solicitações...</p></div>';
            
            try {
                const result = await ViggedAPI.listarPlanosPendentes();
                
                if (result.success && result.data) {
                    const planos = result.data;
                    
                    // Atualizar badge
                    const badge = document.getElementById('planosBadge');
                    if (badge) {
                        if (planos.length > 0) {
                            badge.textContent = planos.length;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    }
                    
                    if (planos.length === 0) {
                        listDiv.innerHTML = '<div class="bg-white rounded-xl shadow-md p-6 text-center"><p class="text-gray-600">Nenhuma solicitação de plano pendente.</p></div>';
                        return;
                    }
                    
                    const planNames = {
                        'essencial': 'Essencial',
                        'profissional': 'Profissional',
                        'enterprise': 'Enterprise'
                    };
                    
                    listDiv.innerHTML = planos.map(plano => `
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">${plano.nome_fantasia || plano.razao_social}</h3>
                                    <p class="text-sm text-gray-600">${plano.razao_social}</p>
                                    <p class="text-sm text-gray-500">CNPJ: ${plano.cnpj}</p>
                                    <p class="text-sm text-gray-500">Email: ${plano.email_corporativo}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-semibold mb-2">
                                        ${planNames[plano.plano_solicitado] || plano.plano_solicitado}
                                    </span>
                                    <p class="text-lg font-bold text-gray-800">R$ ${parseFloat(plano.valor).toFixed(2)}/mês</p>
                                </div>
                            </div>
                            
                            <div class="border-t pt-4 mt-4">
                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">Plano Atual:</p>
                                        <p class="font-semibold">${planNames[plano.plano_atual] || plano.plano_atual || 'Gratuito'}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">Solicitado em:</p>
                                        <p class="font-semibold">${plano.created_at_formatted}</p>
                                    </div>
                                </div>
                                
                                ${plano.observacoes ? `
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 mb-1">Observações:</p>
                                        <p class="text-sm text-gray-800 bg-gray-50 p-3 rounded">${plano.observacoes}</p>
                                    </div>
                                ` : ''}
                                
                                <div class="flex gap-3">
                                    <button onclick="aprovarPlano(${plano.id})" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                        <i class="fas fa-check mr-2"></i>Aprovar
                                    </button>
                                    <button onclick="rejeitarPlano(${plano.id})" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                        <i class="fas fa-times mr-2"></i>Rejeitar
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    listDiv.innerHTML = '<div class="bg-white rounded-xl shadow-md p-6 text-center"><p class="text-red-600">Erro ao carregar solicitações: ' + (result.error || 'Erro desconhecido') + '</p></div>';
                }
            } catch (error) {
                console.error('Erro ao carregar planos pendentes:', error);
                listDiv.innerHTML = '<div class="bg-white rounded-xl shadow-md p-6 text-center"><p class="text-red-600">Erro ao carregar solicitações.</p></div>';
            }
        }
        
        async function aprovarPlano(requestId) {
            if (!confirm('Tem certeza que deseja aprovar esta solicitação de plano?')) {
                return;
            }
            
            try {
                const result = await ViggedAPI.gerenciarPlano(requestId, 'aprovar');
                
                if (result.success) {
                    alert('Plano aprovado com sucesso!');
                    loadPlanosPendentes();
                } else {
                    alert('Erro ao aprovar plano: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao aprovar plano:', error);
                alert('Erro ao aprovar plano. Tente novamente.');
            }
        }
        
        async function rejeitarPlano(requestId) {
            const motivo = prompt('Informe o motivo da rejeição:');
            
            if (!motivo || motivo.trim() === '') {
                alert('O motivo da rejeição é obrigatório.');
                return;
            }
            
            if (!confirm('Tem certeza que deseja rejeitar esta solicitação de plano?')) {
                return;
            }
            
            try {
                const result = await ViggedAPI.gerenciarPlano(requestId, 'rejeitar', motivo.trim());
                
                if (result.success) {
                    alert('Plano rejeitado com sucesso!');
                    loadPlanosPendentes();
                } else {
                    alert('Erro ao rejeitar plano: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao rejeitar plano:', error);
                alert('Erro ao rejeitar plano. Tente novamente.');
            }
        }

        // Gerenciar Vagas
        let currentJobPage = 1;
        let jobs = [];

        async function loadVagas(status = 'todas', search = '') {
            const tbody = document.getElementById('jobs-table-body');
            tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-8 text-center text-gray-500"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div><p class="mt-4">Carregando vagas...</p></td></tr>';
            
            try {
                const params = new URLSearchParams({
                    action: 'list',
                    status: status,
                    page: currentJobPage,
                    limit: itemsPerPage
                });
                
                if (search) {
                    params.append('search', search);
                }
                
                const response = await fetch(`api/admin_vagas.php?${params}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    jobs = result.data;
                    renderJobs();
                    renderJobPagination(result.pagination);
                } else {
                    tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-8 text-center text-red-500">Erro ao carregar vagas: ' + (result.error || 'Erro desconhecido') + '</td></tr>';
                }
            } catch (error) {
                console.error('Erro ao carregar vagas:', error);
                tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-8 text-center text-red-500">Erro ao carregar vagas.</td></tr>';
            }
        }

        function renderJobs() {
            const tbody = document.getElementById('jobs-table-body');
            tbody.innerHTML = '';
            
            if (jobs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">Nenhuma vaga encontrada.</td></tr>';
                return;
            }
            
            jobs.forEach(job => {
                const status = job.status || 'ativa';
                const statusText = status === 'ativa' ? 'Ativa' : status === 'pausada' ? 'Pausada' : 'Encerrada';
                const statusClass = status === 'ativa' ? 'bg-green-100 text-green-800' : 
                                   status === 'pausada' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-red-100 text-red-800';
                const date = job.created_at ? new Date(job.created_at).toLocaleDateString('pt-BR') : '-';
                const empresaNome = job.nome_fantasia || job.razao_social || 'Empresa';
                
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">${job.titulo || 'Sem título'}</div>
                            <div class="text-sm text-gray-500">${(job.descricao || '').substring(0, 50)}...</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${empresaNome}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${job.localizacao || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${job.total_candidaturas || 0}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${job.visualizacoes || 0}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${date}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button onclick="viewJob(${job.id})" class="text-blue-600 hover:text-blue-900" title="Ver"><i class="fas fa-eye"></i></button>
                            <button onclick="editJobStatus(${job.id}, '${status}')" class="text-green-600 hover:text-green-900" title="Alterar Status"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteJob(${job.id})" class="text-red-600 hover:text-red-900" title="Excluir"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
            
            document.getElementById('jobs-showing-start').textContent = (currentJobPage - 1) * itemsPerPage + 1;
            document.getElementById('jobs-showing-end').textContent = Math.min(currentJobPage * itemsPerPage, jobs.length);
            document.getElementById('jobs-total').textContent = jobs.length;
        }

        function renderJobPagination(pagination) {
            const paginationDiv = document.getElementById('jobs-pagination');
            paginationDiv.innerHTML = '';
            
            if (!pagination || pagination.pages <= 1) return;
            
            const totalPages = pagination.pages;
            
            paginationDiv.innerHTML += `
                <button onclick="changeJobPage(${currentJobPage - 1})" 
                        ${currentJobPage === 1 ? 'disabled' : ''} 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Anterior
                </button>
            `;
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentJobPage - 1 && i <= currentJobPage + 1)) {
                    const activeClass = i === currentJobPage ? 'bg-purple-600 text-white' : 'border border-gray-300 hover:bg-gray-50';
                    paginationDiv.innerHTML += `
                        <button onclick="changeJobPage(${i})" class="px-4 py-2 ${activeClass} rounded-lg">
                            ${i}
                        </button>
                    `;
                } else if (i === currentJobPage - 2 || i === currentJobPage + 2) {
                    paginationDiv.innerHTML += '<span class="px-2">...</span>';
                }
            }
            
            paginationDiv.innerHTML += `
                <button onclick="changeJobPage(${currentJobPage + 1})" 
                        ${currentJobPage === totalPages ? 'disabled' : ''} 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Próximo
                </button>
            `;
        }

        async function changeJobPage(page) {
            currentJobPage = page;
            const status = document.getElementById('job-status-filter')?.value || 'todas';
            const search = document.getElementById('job-search')?.value || '';
            await loadVagas(status, search);
        }

        async function filterJobs() {
            currentJobPage = 1;
            const status = document.getElementById('job-status-filter')?.value || 'todas';
            const search = document.getElementById('job-search')?.value || '';
            await loadVagas(status, search);
        }

        function viewJob(jobId) {
            const job = jobs.find(j => j.id === jobId);
            if (job) {
                window.open(`detalhes-vaga.php?id=${jobId}`, '_blank');
            }
        }

        async function editJobStatus(jobId, currentStatus) {
            const statuses = ['ativa', 'pausada', 'encerrada'];
            const statusTexts = { 'ativa': 'Ativa', 'pausada': 'Pausada', 'encerrada': 'Encerrada' };
            const currentIndex = statuses.indexOf(currentStatus);
            const nextIndex = (currentIndex + 1) % statuses.length;
            const newStatus = statuses[nextIndex];
            
            if (!confirm(`Deseja alterar o status da vaga para "${statusTexts[newStatus]}"?`)) {
                return;
            }
            
            try {
                const response = await fetch('api/admin_vagas.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'update_status',
                        job_id: jobId,
                        status: newStatus
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Status da vaga atualizado com sucesso!');
                    await loadVagas();
                } else {
                    alert('Erro ao atualizar status: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao atualizar status:', error);
                alert('Erro ao atualizar status. Tente novamente.');
            }
        }

        async function deleteJob(jobId) {
            if (!confirm('Tem certeza que deseja excluir esta vaga? Esta ação não pode ser desfeita.')) {
                return;
            }
            
            try {
                const response = await fetch('api/admin_vagas.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        job_id: jobId
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Vaga excluída com sucesso!');
                    await loadVagas();
                } else {
                    alert('Erro ao excluir vaga: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao excluir vaga:', error);
                alert('Erro ao excluir vaga. Tente novamente.');
            }
        }

        function exportJobs() {
            alert('Funcionalidade de exportação será implementada em breve.');
        }

        // Relatórios
        async function loadRelatorios() {
            // Carregar dados iniciais do dashboard
            if (stats) {
                updateRelatorioCards();
                renderRelatorioGraficos();
                await loadRelatorioPlanos();
            } else {
                await loadStats();
            }
        }

        async function loadRelatorioPlanos() {
            try {
                const response = await fetch('api/admin_planos.php?action=list&limit=1000');
                const result = await response.json();
                
                if (result.success && result.data) {
                    const empresas = result.data;
                    
                    // Contar planos
                    const planosCount = {
                        'gratuito': 0,
                        'essencial': 0,
                        'profissional': 0,
                        'enterprise': 0
                    };
                    
                    const statusCount = {
                        'ativo': 0,
                        'pendente': 0,
                        'cancelado': 0,
                        'expirado': 0
                    };
                    
                    empresas.forEach(empresa => {
                        const plano = empresa.plano || 'gratuito';
                        const status = empresa.plano_status || 'ativo';
                        
                        if (planosCount.hasOwnProperty(plano)) {
                            planosCount[plano]++;
                        }
                        if (statusCount.hasOwnProperty(status)) {
                            statusCount[status]++;
                        }
                    });
                    
                    const planNames = {
                        'gratuito': 'Gratuito',
                        'essencial': 'Essencial',
                        'profissional': 'Profissional',
                        'enterprise': 'Enterprise'
                    };
                    
                    let html = `
                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h4 class="font-bold text-gray-800 mb-4">Distribuição por Plano</h4>
                                <div class="space-y-3">
                    `;
                    
                    Object.entries(planosCount).forEach(([plano, total]) => {
                        const totalEmpresas = empresas.length || 1;
                        const percentage = (total / totalEmpresas * 100).toFixed(1);
                        const color = plano === 'gratuito' ? 'bg-gray-500' :
                                     plano === 'essencial' ? 'bg-blue-500' :
                                     plano === 'profissional' ? 'bg-purple-500' :
                                     'bg-yellow-500';
                        
                        html += `
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700">${planNames[plano]}</span>
                                    <span class="text-gray-600">${total} (${percentage}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="${color} h-3 rounded-full" style="width: ${percentage}%"></div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-4">Status dos Planos</h4>
                                <div class="space-y-3">
                    `;
                    
                    Object.entries(statusCount).forEach(([status, total]) => {
                        const totalEmpresas = empresas.length || 1;
                        const percentage = (total / totalEmpresas * 100).toFixed(1);
                        const statusText = status === 'ativo' ? 'Ativos' :
                                          status === 'pendente' ? 'Pendentes' :
                                          status === 'cancelado' ? 'Cancelados' : 'Expirados';
                        const color = status === 'ativo' ? 'bg-green-500' :
                                     status === 'pendente' ? 'bg-yellow-500' :
                                     status === 'cancelado' ? 'bg-red-500' : 'bg-gray-500';
                        
                        html += `
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700">${statusText}</span>
                                    <span class="text-gray-600">${total} (${percentage}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="${color} h-3 rounded-full" style="width: ${percentage}%"></div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <h4 class="font-bold text-gray-800 mb-4">Resumo</h4>
                            <div class="grid md:grid-cols-4 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600">Total de Empresas</p>
                                    <p class="text-2xl font-bold text-gray-800">${empresas.length}</p>
                                </div>
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600">Com Plano Pago</p>
                                    <p class="text-2xl font-bold text-blue-600">${planosCount.essencial + planosCount.profissional + planosCount.enterprise}</p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600">Planos Ativos</p>
                                    <p class="text-2xl font-bold text-green-600">${statusCount.ativo}</p>
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600">Pendentes</p>
                                    <p class="text-2xl font-bold text-yellow-600">${statusCount.pendente}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('relatorio-planos').innerHTML = html;
                } else {
                    document.getElementById('relatorio-planos').innerHTML = '<p class="text-red-500 text-center py-8">Erro ao carregar relatório de planos.</p>';
                }
            } catch (error) {
                console.error('Erro ao carregar relatório de planos:', error);
                document.getElementById('relatorio-planos').innerHTML = '<p class="text-red-500 text-center py-8">Erro ao carregar relatório de planos.</p>';
            }
        }

        function updateRelatorioCards() {
            if (!stats) return;
            
            document.getElementById('relatorio-total-vagas').textContent = formatNumber(stats.total_vagas || 0);
            document.getElementById('relatorio-total-candidaturas').textContent = formatNumber(stats.total_candidaturas || 0);
            document.getElementById('relatorio-taxa-conversao').textContent = (stats.taxa_conversao || 0).toFixed(1) + '%';
            document.getElementById('relatorio-taxa-aprovacao').textContent = (stats.taxa_aprovacao || 0).toFixed(1) + '%';
        }

        function renderRelatorioGraficos() {
            if (!stats) return;
            
            // Gráfico de vagas por status
            const vagasStatus = stats.vagas_por_status || {};
            const vagasStatusDiv = document.getElementById('grafico-vagas-status');
            let html = '<div class="space-y-3">';
            
            Object.entries(vagasStatus).forEach(([status, total]) => {
                const statusText = status === 'ativa' ? 'Ativas' : status === 'pausada' ? 'Pausadas' : 'Encerradas';
                const color = status === 'ativa' ? 'bg-green-500' : status === 'pausada' ? 'bg-yellow-500' : 'bg-red-500';
                const totalVagas = stats.total_vagas || 1;
                const percentage = (total / totalVagas * 100).toFixed(1);
                
                html += `
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">${statusText}</span>
                            <span class="text-gray-600">${total} (${percentage}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="${color} h-3 rounded-full" style="width: ${percentage}%"></div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            vagasStatusDiv.innerHTML = html;
            
            // Gráfico de candidaturas por status
            const candidaturasStatus = stats.candidaturas_por_status || {};
            const candidaturasStatusDiv = document.getElementById('grafico-candidaturas-status');
            html = '<div class="space-y-3">';
            
            Object.entries(candidaturasStatus).forEach(([status, total]) => {
                const statusText = status === 'pendente' ? 'Pendentes' : 
                                 status === 'em_analise' ? 'Em Análise' :
                                 status === 'aprovada' ? 'Aprovadas' :
                                 status === 'rejeitada' ? 'Rejeitadas' : 'Canceladas';
                const color = status === 'aprovada' ? 'bg-green-500' : 
                             status === 'rejeitada' ? 'bg-red-500' :
                             status === 'pendente' ? 'bg-yellow-500' :
                             status === 'em_analise' ? 'bg-blue-500' : 'bg-gray-500';
                const totalCandidaturas = stats.total_candidaturas || 1;
                const percentage = (total / totalCandidaturas * 100).toFixed(1);
                
                html += `
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">${statusText}</span>
                            <span class="text-gray-600">${total} (${percentage}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="${color} h-3 rounded-full" style="width: ${percentage}%"></div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            candidaturasStatusDiv.innerHTML = html;
        }

        async function gerarRelatorio() {
            const dataInicio = document.getElementById('relatorio-data-inicio').value;
            const dataFim = document.getElementById('relatorio-data-fim').value;
            const tipo = document.getElementById('relatorio-tipo').value;
            
            const detalhesDiv = document.getElementById('relatorio-detalhes');
            detalhesDiv.innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div></div>';
            
            // Por enquanto, mostrar dados do dashboard
            // Em produção, isso chamaria uma API específica de relatórios
            setTimeout(() => {
                let html = '<div class="space-y-4">';
                html += '<h4 class="font-bold text-gray-800 mb-4">Resumo do Período</h4>';
                
                if (stats) {
                    html += `
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total de Vagas</p>
                                <p class="text-2xl font-bold text-gray-800">${stats.total_vagas || 0}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total de Candidaturas</p>
                                <p class="text-2xl font-bold text-gray-800">${stats.total_candidaturas || 0}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Taxa de Conversão</p>
                                <p class="text-2xl font-bold text-gray-800">${(stats.taxa_conversao || 0).toFixed(1)}%</p>
                            </div>
                        </div>
                    `;
                }
                
                html += '<p class="text-sm text-gray-500 mt-4">Relatório completo será gerado com base nos filtros selecionados.</p>';
                html += '</div>';
                
                detalhesDiv.innerHTML = html;
            }, 500);
        }

        function exportRelatorio(format) {
            alert(`Exportação em formato ${format.toUpperCase()} será implementada em breve.`);
        }

        // Gerenciar Planos das Empresas
        let currentEmpresaPlanoPage = 1;
        let empresasPlanos = [];

        async function loadEmpresasPlanos(plano = 'todas', status = 'todas', search = '') {
            const tbody = document.getElementById('empresas-planos-table-body');
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div><p class="mt-4">Carregando empresas...</p></td></tr>';
            
            try {
                const params = new URLSearchParams({
                    action: 'list',
                    plano: plano,
                    status: status,
                    page: currentEmpresaPlanoPage,
                    limit: itemsPerPage
                });
                
                if (search) {
                    params.append('search', search);
                }
                
                const response = await fetch(`api/admin_planos.php?${params}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    empresasPlanos = result.data;
                    renderEmpresasPlanos();
                    renderEmpresasPlanosPagination(result.pagination);
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-red-500">Erro ao carregar empresas: ' + (result.error || 'Erro desconhecido') + '</td></tr>';
                }
            } catch (error) {
                console.error('Erro ao carregar empresas:', error);
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-red-500">Erro ao carregar empresas.</td></tr>';
            }
        }

        function renderEmpresasPlanos() {
            const tbody = document.getElementById('empresas-planos-table-body');
            tbody.innerHTML = '';
            
            if (empresasPlanos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">Nenhuma empresa encontrada.</td></tr>';
                return;
            }
            
            const planNames = {
                'gratuito': 'Gratuito',
                'essencial': 'Essencial',
                'profissional': 'Profissional',
                'enterprise': 'Enterprise'
            };
            
            empresasPlanos.forEach(empresa => {
                const empresaNome = empresa.nome_fantasia || empresa.razao_social || 'Sem nome';
                const plano = empresa.plano || 'gratuito';
                const planoStatus = empresa.plano_status || 'ativo';
                
                const planoBadge = plano === 'gratuito' ? 'bg-gray-100 text-gray-800' :
                                 plano === 'essencial' ? 'bg-blue-100 text-blue-800' :
                                 plano === 'profissional' ? 'bg-purple-100 text-purple-800' :
                                 'bg-yellow-100 text-yellow-800';
                
                const statusBadge = planoStatus === 'ativo' ? 'bg-green-100 text-green-800' :
                                  planoStatus === 'pendente' ? 'bg-yellow-100 text-yellow-800' :
                                  planoStatus === 'cancelado' ? 'bg-red-100 text-red-800' :
                                  'bg-gray-100 text-gray-800';
                
                const temSolicitacaoPendente = empresa.solicitacoes_pendentes > 0;
                
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">${empresaNome}</div>
                            <div class="text-sm text-gray-500">${empresa.email_corporativo || ''}</div>
                            ${temSolicitacaoPendente ? '<span class="inline-block mt-1 bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Solicitação pendente</span>' : ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${empresa.cnpj || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${planoBadge}">
                                ${planNames[plano] || plano}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusBadge}">
                                ${planoStatus === 'ativo' ? 'Ativo' : planoStatus === 'pendente' ? 'Pendente' : planoStatus === 'cancelado' ? 'Cancelado' : planoStatus}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="font-semibold">${empresa.vagas_ativas || 0}</span> / ${empresa.total_vagas || 0}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${empresa.total_candidaturas || 0}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button onclick="openEditPlanoModal(${empresa.id})" class="text-blue-600 hover:text-blue-900" title="Editar Plano">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${plano !== 'gratuito' ? `
                                <button onclick="removePlano(${empresa.id})" class="text-red-600 hover:text-red-900" title="Remover Plano">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            ` : ''}
                        </td>
                    </tr>
                `;
            });
            
            document.getElementById('empresas-planos-showing-start').textContent = (currentEmpresaPlanoPage - 1) * itemsPerPage + 1;
            document.getElementById('empresas-planos-showing-end').textContent = Math.min(currentEmpresaPlanoPage * itemsPerPage, empresasPlanos.length);
            document.getElementById('empresas-planos-total').textContent = empresasPlanos.length;
        }

        function renderEmpresasPlanosPagination(pagination) {
            const paginationDiv = document.getElementById('empresas-planos-pagination');
            paginationDiv.innerHTML = '';
            
            if (!pagination || pagination.pages <= 1) return;
            
            const totalPages = pagination.pages;
            
            paginationDiv.innerHTML += `
                <button onclick="changeEmpresaPlanoPage(${currentEmpresaPlanoPage - 1})" 
                        ${currentEmpresaPlanoPage === 1 ? 'disabled' : ''} 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Anterior
                </button>
            `;
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentEmpresaPlanoPage - 1 && i <= currentEmpresaPlanoPage + 1)) {
                    const activeClass = i === currentEmpresaPlanoPage ? 'bg-purple-600 text-white' : 'border border-gray-300 hover:bg-gray-50';
                    paginationDiv.innerHTML += `
                        <button onclick="changeEmpresaPlanoPage(${i})" class="px-4 py-2 ${activeClass} rounded-lg">
                            ${i}
                        </button>
                    `;
                } else if (i === currentEmpresaPlanoPage - 2 || i === currentEmpresaPlanoPage + 2) {
                    paginationDiv.innerHTML += '<span class="px-2">...</span>';
                }
            }
            
            paginationDiv.innerHTML += `
                <button onclick="changeEmpresaPlanoPage(${currentEmpresaPlanoPage + 1})" 
                        ${currentEmpresaPlanoPage === totalPages ? 'disabled' : ''} 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Próximo
                </button>
            `;
        }

        async function changeEmpresaPlanoPage(page) {
            currentEmpresaPlanoPage = page;
            const plano = document.getElementById('empresa-plano-filter')?.value || 'todas';
            const status = document.getElementById('empresa-plano-status-filter')?.value || 'todas';
            const search = document.getElementById('empresa-plano-search')?.value || '';
            await loadEmpresasPlanos(plano, status, search);
        }

        async function filterEmpresasPlanos() {
            currentEmpresaPlanoPage = 1;
            const plano = document.getElementById('empresa-plano-filter')?.value || 'todas';
            const status = document.getElementById('empresa-plano-status-filter')?.value || 'todas';
            const search = document.getElementById('empresa-plano-search')?.value || '';
            await loadEmpresasPlanos(plano, status, search);
        }

        function openEditPlanoModal(companyId) {
            const empresa = empresasPlanos.find(e => e.id === companyId);
            if (!empresa) {
                alert('Empresa não encontrada');
                return;
            }
            
            const modal = document.getElementById('edit-plano-modal');
            document.getElementById('edit-plano-company-id').value = companyId;
            document.getElementById('edit-plano-empresa-nome').textContent = empresa.nome_fantasia || empresa.razao_social;
            document.getElementById('edit-plano-empresa-cnpj').textContent = 'CNPJ: ' + (empresa.cnpj || '-');
            document.getElementById('edit-plano-plano').value = empresa.plano || 'gratuito';
            document.getElementById('edit-plano-status').value = empresa.plano_status || 'ativo';
            
            modal.classList.add('active');
        }

        function closeEditPlanoModal() {
            document.getElementById('edit-plano-modal').classList.remove('active');
            document.getElementById('edit-plano-form').reset();
        }

        async function savePlano(event) {
            event.preventDefault();
            
            const companyId = parseInt(document.getElementById('edit-plano-company-id').value);
            const novoPlano = document.getElementById('edit-plano-plano').value;
            const novoStatus = document.getElementById('edit-plano-status').value;
            
            await updatePlano(companyId, novoPlano, novoStatus);
            closeEditPlanoModal();
        }

        async function updatePlano(companyId, novoPlano, novoStatus) {
            try {
                const response = await fetch('api/admin_planos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'update_plano',
                        company_id: companyId,
                        plano: novoPlano,
                        plano_status: novoStatus
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Plano atualizado com sucesso!');
                    await loadEmpresasPlanos();
                } else {
                    alert('Erro ao atualizar plano: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao atualizar plano:', error);
                alert('Erro ao atualizar plano. Tente novamente.');
            }
        }

        async function removePlano(companyId) {
            const empresa = empresasPlanos.find(e => e.id === companyId);
            if (!empresa) {
                alert('Empresa não encontrada');
                return;
            }
            
            const planNames = {
                'gratuito': 'Gratuito',
                'essencial': 'Essencial',
                'profissional': 'Profissional',
                'enterprise': 'Enterprise'
            };
            
            if (!confirm(`Tem certeza que deseja remover o plano "${planNames[empresa.plano]}" da empresa "${empresa.nome_fantasia || empresa.razao_social}"?\n\nA empresa voltará para o plano gratuito.`)) {
                return;
            }
            
            try {
                const response = await fetch('api/admin_planos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'remove_plano',
                        company_id: companyId
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Plano removido com sucesso! A empresa voltou para o plano gratuito.');
                    await loadEmpresasPlanos();
                } else {
                    alert('Erro ao remover plano: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao remover plano:', error);
                alert('Erro ao remover plano. Tente novamente.');
            }
        }

        // Initialize dashboard on load
        (async function() {
            await loadStats();
            await loadUsers();
            await loadCompanies();
            await loadPlanosPendentes();
        })();
    </script>
<?php
// Admin não usa footer padrão devido ao layout customizado com sidebar
// Footer removido para manter layout administrativo limpo
?>
</body>
</html>

