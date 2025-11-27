<?php
// Configurar título da página
$title = 'Perfil da Empresa';

// Verificar autenticação
require_once 'config/auth.php';
startSecureSession();
requireAuth(USER_TYPE_COMPANY);

// Incluir head
include 'includes/head.php';

// Incluir navegação autenticada
$navType = 'authenticated';
include 'includes/nav.php';

// Exibir mensagens de erro/sucesso
$errors = $_SESSION['perfil_errors'] ?? [];
$success = $_SESSION['perfil_success'] ?? null;
unset($_SESSION['perfil_errors'], $_SESSION['perfil_success']);
?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Company Header -->
        <div class="bg-white rounded-lg shadow-md mb-6 relative">
            <div class="h-32 bg-gradient-to-r from-purple-600 to-purple-400 rounded-t-lg"></div>
            <div class="px-6 pb-6">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between">
                    <div class="flex items-end space-x-4">
                        <div class="relative -mt-16">
                            <img id="companyLogo" src="/placeholder.svg?height=120&width=120" alt="Logo da empresa" class="w-32 h-32 rounded-lg border-4 border-white object-cover bg-white">
                            <button onclick="openUploadLogoModal()" class="absolute bottom-0 right-0 bg-purple-600 text-white p-2 rounded-full hover:bg-purple-700 transition" title="Alterar Logo">
                                <i class="fas fa-camera text-sm"></i>
                            </button>
                        </div>
                        <div class="pb-2">
                            <h1 id="companyName" class="text-2xl font-bold text-gray-900">Tech Solutions Ltda</h1>
                            <p id="companyIndustry" class="text-gray-600">Tecnologia da Informação</p>
                            <p class="text-gray-500 text-sm mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <span id="companyLocation">São Paulo, SP</span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-3">
                        <button onclick="openEditCompanyModal()" class="px-4 py-2 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 transition">
                            <i class="fas fa-edit mr-2"></i>Editar Perfil
                        </button>
                        <button onclick="openJobModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-plus mr-2"></i>Publicar Vaga
                        </button>
                        
                        <!-- Configurações da Conta -->
                        <div class="relative z-50">
                            <button onclick="toggleAccountSettings()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition flex items-center">
                                <i class="fas fa-cog mr-2"></i>Configurações
                                <i class="fas fa-chevron-down ml-2 text-xs" id="settingsChevron"></i>
                            </button>
                            <div id="accountSettingsMenu" class="hidden absolute top-full right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden min-w-[200px]">
                                <button onclick="openChangePasswordModal()" class="w-full text-left px-4 py-3 hover:bg-gray-50 transition flex items-center text-gray-700">
                                    <i class="fas fa-key mr-3 text-purple-600"></i>
                                    Trocar Senha
                                </button>
                                <button onclick="openChangeEmailModal()" class="w-full text-left px-4 py-3 hover:bg-gray-50 transition flex items-center text-gray-700">
                                    <i class="fas fa-envelope mr-3 text-purple-600"></i>
                                    Trocar Email
                                </button>
                                <div class="border-t border-gray-200"></div>
                                <button onclick="openDeleteAccountModal(); toggleAccountSettings();" class="w-full text-left px-4 py-3 hover:bg-red-50 transition flex items-center text-red-600">
                                    <i class="fas fa-trash-alt mr-3"></i>
                                    Excluir Conta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Current Plan Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Plano Atual</h3>
                    <div id="currentPlan" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Plano:</span>
                            <span id="planName" class="font-semibold text-purple-600">Gratuito</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span id="planStatus" class="text-sm px-2 py-1 rounded">
                                <span class="bg-green-100 text-green-800">Ativo</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Vagas ativas:</span>
                            <span class="font-semibold">0/2</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Candidatos:</span>
                            <span class="font-semibold">0/50</span>
                        </div>
                    </div>
                    <div id="planPendingMessage" class="hidden mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-clock mr-2"></i>
                            Você possui uma solicitação de plano pendente de aprovação.
                        </p>
                    </div>
                    <button onclick="openPlansModal()" class="w-full mt-4 bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-crown mr-2"></i>Fazer Upgrade
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Estatísticas</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Visualizações</span>
                            <span class="font-semibold text-lg">1,234</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Candidaturas</span>
                            <span class="font-semibold text-lg">45</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Taxa de resposta</span>
                            <span class="font-semibold text-lg">87%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- About Company -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Sobre a Empresa</h3>
                        <button onclick="openEditCompanyModal()" class="text-purple-600 hover:text-purple-700">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <p id="companyAbout" class="text-gray-700 text-sm leading-relaxed">
                        Somos uma empresa de tecnologia comprometida com a diversidade e inclusão. Buscamos talentos PCD para compor nosso time e construir soluções inovadoras juntos.
                    </p>
                </div>

                <!-- Tabs para navegação -->
                <div class="bg-white rounded-lg shadow-md mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button onclick="switchTab('vagas')" id="tab-vagas" class="tab-button active px-6 py-4 text-sm font-medium text-purple-600 border-b-2 border-purple-600">
                                <i class="fas fa-briefcase mr-2"></i>Minhas Vagas
                            </button>
                            <button onclick="switchTab('candidatos')" id="tab-candidatos" class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300">
                                <i class="fas fa-users mr-2"></i>Gerenciar Candidatos
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Conteúdo: Minhas Vagas -->
                <div id="content-vagas" class="tab-content">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">
                                <i class="fas fa-briefcase mr-2 text-purple-600"></i>Minhas Vagas Publicadas
                            </h3>
                            <button onclick="openJobModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                                <i class="fas fa-plus mr-2"></i>Publicar Nova Vaga
                            </button>
                        </div>
                        
                        <!-- Filtros de vagas -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <select id="filterJobStatus" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="ativa">Vagas Ativas</option>
                                <option value="pausada">Vagas Pausadas</option>
                                <option value="encerrada">Vagas Encerradas</option>
                                <option value="todas">Todas as Vagas</option>
                            </select>
                            <input type="text" id="filterJobTitle" placeholder="Buscar por título..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <button onclick="loadActiveJobs()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                <i class="fas fa-search mr-2"></i>Buscar
                            </button>
                        </div>
                        
                        <div id="activeJobsList" class="space-y-4">
                            <p class="text-gray-500 text-center py-8">Carregando vagas...</p>
                        </div>
                    </div>
                </div>

                <!-- Conteúdo: Gerenciar Candidatos -->
                <div id="content-candidatos" class="tab-content hidden">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">
                                <i class="fas fa-users mr-2 text-purple-600"></i>Gerenciar Candidatos
                            </h3>
                        </div>
                        
                        <!-- Filtros -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <select id="filterJob" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="">Selecione uma vaga</option>
                            </select>
                            <select id="filterStatus" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="todas">Todos os status</option>
                                <option value="pendente">Pendente</option>
                                <option value="em_analise">Em Análise</option>
                                <option value="aprovada">Aprovada</option>
                                <option value="rejeitada">Reprovada</option>
                            </select>
                            <input type="text" id="filterNome" placeholder="Buscar por nome..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <input type="text" id="filterEmail" placeholder="Buscar por e-mail..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        
                        <div id="candidatesList" class="space-y-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                                <i class="fas fa-info-circle text-blue-600 text-2xl mb-2"></i>
                                <p class="text-blue-800 font-medium">Selecione uma vaga acima para visualizar os candidatos</p>
                                <p class="text-blue-600 text-sm mt-1">Ou clique no número de candidatos em uma vaga para ver diretamente</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plans Modal -->
    <div id="plansModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-5xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Escolha seu Plano</h3>
                    <button onclick="closePlansModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Plano Essencial -->
                    <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-purple-300 transition">
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Plano Essencial</h4>
                        <p class="text-gray-600 text-sm mb-4">Inclusão Consciente</p>
                        <div class="mb-6">
                            <span class="text-4xl font-bold text-purple-600">R$199</span>
                            <span class="text-gray-600">/mês</span>
                        </div>
                        <ul class="space-y-3 mb-6 text-sm">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>3 vagas ativas por mês</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Currículo das vagas até 25 dias</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Vagas destacadas nas 48h iniciais</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Acesso a cursos e materiais sobre comunicação PCD</span>
                            </li>
                        </ul>
                        <button onclick="subscribePlan('Essencial', 199)" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition">
                            Contratar Plano
                        </button>
                    </div>

                    <!-- Plano Profissional -->
                    <div class="border-2 border-purple-600 rounded-lg p-6 relative">
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-purple-600 text-white px-4 py-1 rounded-full text-xs font-semibold">
                            MAIS POPULAR
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Plano Profissional</h4>
                        <p class="text-gray-600 text-sm mb-4">Diversidade em Foco</p>
                        <div class="mb-6">
                            <span class="text-4xl font-bold text-purple-600">R$399</span>
                            <span class="text-gray-600">/mês</span>
                        </div>
                        <ul class="space-y-3 mb-6 text-sm">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>10 vagas ativas por mês</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Currículo das vagas até 45 dias</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Vagas em destaque na página principal</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Estatísticas e métricas de desempenho das vagas</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Envio automático para vagas banco de talentos</span>
                            </li>
                        </ul>
                        <button onclick="subscribePlan('Profissional', 399)" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition">
                            Contratar Plano
                        </button>
                    </div>

                    <!-- Plano Enterprise -->
                    <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-purple-300 transition">
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Plano Enterprise</h4>
                        <p class="text-gray-600 text-sm mb-4">Inclusão Total</p>
                        <div class="mb-6">
                            <span class="text-4xl font-bold text-purple-600">R$799</span>
                            <span class="text-gray-600">/mês</span>
                        </div>
                        <ul class="space-y-3 mb-6 text-sm">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Vagas ilimitadas</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Currículo-das vagas até 60 dias</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Destaque premium em todas as páginas</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Consultoria especializada em inclusão e acessibilidade</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Acesso a consultoria de inclusão e acessibilidade</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Relatórios avançados e segmentados</span>
                            </li>
                        </ul>
                        <button onclick="subscribePlan('Enterprise', 799)" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition">
                            Contratar Plano
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Logo Modal -->
    <div id="uploadLogoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Alterar Logo da Empresa</h3>
                    <button onclick="closeUploadLogoModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="uploadLogoForm" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selecione uma imagem</label>
                        <input type="file" id="logoFile" name="logo" accept="image/jpeg,image/png,image/gif,image/webp" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Formatos aceitos: JPEG, PNG, GIF, WebP. Tamanho máximo: 5MB</p>
                    </div>
                    <div id="logoPreview" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                        <img id="logoPreviewImg" src="" alt="Preview" class="w-32 h-32 rounded-lg border-2 border-gray-300 object-cover">
                    </div>
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeUploadLogoModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-upload mr-2"></i>Enviar Logo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Company Modal -->
    <div id="editCompanyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Editar Informações da Empresa</h3>
                    <button onclick="closeEditCompanyModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="editCompanyForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Empresa</label>
                        <input type="text" id="editCompanyName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Setor</label>
                        <input type="text" id="editCompanyIndustry" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Localização</label>
                        <input type="text" id="editCompanyLocation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sobre a Empresa</label>
                        <textarea id="editCompanyAbout" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeEditCompanyModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- New Job Modal -->
    <div id="jobModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Publicar Nova Vaga</h3>
                    <button onclick="closeJobModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="newJobForm" action="processar_vaga.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Título da Vaga</label>
                        <input type="text" id="jobTitle" name="titulo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Localização</label>
                        <input type="text" id="jobLocation" name="localizacao" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato</label>
                        <select id="jobType" name="tipo_contrato" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            <option value="CLT">CLT</option>
                            <option value="PJ">PJ</option>
                            <option value="Estágio">Estágio</option>
                            <option value="Temporário">Temporário</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Faixa Salarial</label>
                        <input type="text" id="jobSalary" name="faixa_salarial" placeholder="Ex: R$ 5.000 - R$ 8.000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descrição da Vaga</label>
                        <textarea id="jobDescription" name="descricao" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Requisitos</label>
                        <textarea id="jobRequirements" name="requisitos" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent" placeholder="Liste os requisitos desejados..."></textarea>
                    </div>
                    <?php
                    // Adicionar token CSRF
                    require_once 'includes/csrf.php';
                    echo csrfField();
                    ?>
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeJobModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            Publicar Vaga
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Gerenciamento de Candidato -->
    <div id="manageCandidateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Gerenciar Candidato</h3>
                    <button onclick="closeManageCandidateModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form onsubmit="event.preventDefault(); saveCandidateStatus();" class="space-y-4">
                    <input type="hidden" id="manageApplicationId">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="manageStatus" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="pendente">Pendente</option>
                            <option value="em_analise">Em Análise</option>
                            <option value="aprovada">Aprovada</option>
                            <option value="rejeitada">Reprovada</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Feedback <span class="text-red-500" id="feedbackRequired" style="display:none;">*</span></label>
                        <textarea id="manageFeedback" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Digite o feedback para o candidato..."></textarea>
                        <p class="text-sm text-gray-500 mt-1">Obrigatório quando o status for "Reprovada"</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Avaliação (1-5 estrelas)</label>
                        <select id="manageAvaliacao" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Não avaliar</option>
                            <option value="1">1 estrela</option>
                            <option value="2">2 estrelas</option>
                            <option value="3">3 estrelas</option>
                            <option value="4">4 estrelas</option>
                            <option value="5">5 estrelas</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeManageCandidateModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Delete Account Modal -->
    <div id="deleteAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-red-600">Excluir Conta</h3>
                    <button onclick="closeDeleteAccountModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="mb-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-red-800 font-semibold mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Atenção: Esta ação é irreversível!
                        </p>
                        <p class="text-sm text-red-700">
                            Ao excluir sua conta, todos os seus dados serão permanentemente removidos, incluindo:
                        </p>
                        <ul class="list-disc list-inside text-sm text-red-700 mt-2 space-y-1">
                            <li>Seu perfil completo</li>
                            <li>Todas as suas vagas publicadas</li>
                            <li>Todas as candidaturas recebidas</li>
                            <li>Seus arquivos (logo, documentos)</li>
                            <li>Histórico de atividades</li>
                        </ul>
                    </div>
                    
                    <p class="text-sm text-gray-700 mb-4">
                        Para confirmar a exclusão, digite <strong class="text-red-600">EXCLUIR</strong> no campo abaixo:
                    </p>
                    
                    <input 
                        type="text" 
                        id="confirmDeleteInput" 
                        placeholder="Digite EXCLUIR para confirmar"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 focus:border-transparent"
                        autocomplete="off"
                    >
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button 
                        onclick="closeDeleteAccountModal()" 
                        class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        id="confirmDeleteButton"
                        onclick="confirmDeleteAccount()" 
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                    >
                        <i class="fas fa-trash-alt mr-2"></i>Excluir Conta
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php
// Incluir footer escuro (para páginas de perfil)
$footerStyle = 'dark';
include 'includes/footer.php';
?>

    <script src="assets/js/api.js"></script>
    <script>
        let companyData = null;
        let jobsData = [];

        // Load company data from API
        async function loadCompanyData() {
            const companyNameElement = document.getElementById('companyName');
            if (!companyNameElement) {
                console.error('Elemento companyName não encontrado');
                return;
            }
            
            companyNameElement.textContent = 'Carregando...';
            
            try {
                console.log('Carregando dados da empresa...');
                const response = await obterDadosEmpresa();
                console.log('Resposta recebida:', response);
                
                if (response && response.success && response.data) {
                    companyData = response.data;
                    console.log('Dados da empresa carregados:', companyData);
                    displayCompanyData();
                    // Carregar vagas após carregar dados da empresa
                    setTimeout(() => {
                        loadActiveJobs();
                    }, 100);
                } else {
                    const errorMsg = (response && response.error) ? response.error : 'Erro desconhecido';
                    console.error('Erro ao carregar dados da empresa:', errorMsg, response);
                    companyNameElement.textContent = 'Erro ao carregar dados';
                    companyNameElement.style.color = 'red';
                    
                    // Mostrar mensagem de erro mais detalhada
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mt-2';
                    errorDiv.innerHTML = `
                        <p class="text-red-800 font-medium">Erro ao carregar dados da empresa</p>
                        <p class="text-red-600 text-sm mt-1">${errorMsg}</p>
                        <button onclick="loadCompanyData()" class="mt-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                            <i class="fas fa-redo mr-2"></i>Tentar Novamente
                        </button>
                    `;
                    companyNameElement.parentElement.appendChild(errorDiv);
                }
            } catch (error) {
                console.error('Erro ao carregar dados da empresa:', error);
                companyNameElement.textContent = 'Erro ao carregar dados';
                companyNameElement.style.color = 'red';
            }
        }

        function displayCompanyData() {
            if (!companyData) {
                console.error('companyData é null ou undefined');
                return;
            }

            console.log('Exibindo dados da empresa:', companyData);

            // Nome da empresa
            const companyNameEl = document.getElementById('companyName');
            if (companyNameEl) {
                companyNameEl.textContent = companyData.nome_fantasia || companyData.razao_social || 'Sem nome';
                companyNameEl.style.color = ''; // Resetar cor se estava em vermelho
            } else {
                console.error('Elemento companyName não encontrado');
            }
            
            // Setor/Indústria
            const companyIndustryEl = document.getElementById('companyIndustry');
            if (companyIndustryEl) {
                companyIndustryEl.textContent = companyData.setor || '-';
            } else {
                console.warn('Elemento companyIndustry não encontrado');
            }
            
            // Localização
            const companyLocationEl = document.getElementById('companyLocation');
            if (companyLocationEl) {
                const location = [];
                if (companyData.cidade) location.push(companyData.cidade);
                if (companyData.estado) location.push(companyData.estado);
                companyLocationEl.textContent = location.length > 0 ? location.join(', ') : '-';
            } else {
                console.warn('Elemento companyLocation não encontrado');
            }
            
            // Sobre
            const companyAboutEl = document.getElementById('companyAbout');
            if (companyAboutEl) {
                companyAboutEl.textContent = companyData.descricao || 'Nenhuma descrição disponível.';
            } else {
                console.warn('Elemento companyAbout não encontrado');
            }
            
            // Logo
            const logoImg = document.getElementById('companyLogo');
            if (logoImg) {
                if (companyData.logo_path) {
                    const logoUrl = companyData.logo_path.startsWith('http') ? companyData.logo_path : '<?php echo BASE_URL; ?>/' + companyData.logo_path;
                    logoImg.src = logoUrl;
                    logoImg.onerror = function() {
                        // Se a imagem não carregar, usar placeholder padrão
                        this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgZmlsbD0iI2U1ZTdlYiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5Y2EzYWYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5Mb2dvPC90ZXh0Pjwvc3ZnPg==';
                    };
                } else {
                    logoImg.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgZmlsbD0iI2U1ZTdlYiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5Y2EzYWYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5Mb2dvPC90ZXh0Pjwvc3ZnPg==';
                }
            } else {
                console.warn('Elemento companyLogo não encontrado');
            }
            
            // Plano
            const planNames = {
                'gratuito': 'Gratuito',
                'essencial': 'Essencial',
                'profissional': 'Profissional',
                'enterprise': 'Enterprise'
            };
            const planNameEl = document.getElementById('planName');
            if (planNameEl) {
                planNameEl.textContent = planNames[companyData.plano] || 'Gratuito';
            } else {
                console.warn('Elemento planName não encontrado');
            }
            
            // Status do plano
            const planStatus = companyData.plano_status || 'ativo';
            const statusElement = document.getElementById('planStatus');
            const pendingMessage = document.getElementById('planPendingMessage');
            
            if (statusElement) {
                if (planStatus === 'pendente') {
                    statusElement.innerHTML = '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">Pendente</span>';
                    if (pendingMessage) pendingMessage.classList.remove('hidden');
                } else if (planStatus === 'ativo') {
                    statusElement.innerHTML = '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Ativo</span>';
                    if (pendingMessage) pendingMessage.classList.add('hidden');
                } else if (planStatus === 'cancelado') {
                    statusElement.innerHTML = '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Cancelado</span>';
                    if (pendingMessage) pendingMessage.classList.add('hidden');
                } else {
                    statusElement.innerHTML = '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-semibold">' + planStatus + '</span>';
                    if (pendingMessage) pendingMessage.classList.add('hidden');
                }
            } else {
                console.warn('Elemento planStatus não encontrado');
            }
            
            // Estatísticas - verificar cada elemento antes de atualizar
            const vagasAtivasEl = document.getElementById('vagasAtivas');
            if (vagasAtivasEl) {
                vagasAtivasEl.textContent = companyData.vagas_ativas || 0;
            }
            
            const totalCandidatosEl = document.getElementById('totalCandidatos');
            if (totalCandidatosEl) {
                totalCandidatosEl.textContent = companyData.total_candidaturas || 0;
            }
            
            const totalVagasEl = document.getElementById('totalVagas');
            if (totalVagasEl) {
                totalVagasEl.textContent = companyData.total_vagas || 0;
            }
            
            const totalCandidaturasEl = document.getElementById('totalCandidaturas');
            if (totalCandidaturasEl) {
                totalCandidaturasEl.textContent = companyData.total_candidaturas || 0;
            }
            
            const vagasAtivasStatsEl = document.getElementById('vagasAtivasStats');
            if (vagasAtivasStatsEl) {
                vagasAtivasStatsEl.textContent = companyData.vagas_ativas || 0;
            }
            
            console.log('Dados da empresa exibidos com sucesso');
        }

        async function loadActiveJobs() {
            const jobsListElement = document.getElementById('activeJobsList');
            if (!jobsListElement) {
                console.error('Elemento activeJobsList não encontrado');
                return;
            }
            
            jobsListElement.innerHTML = '<p class="text-gray-500 text-center py-8">Carregando vagas...</p>';
            
            const status = document.getElementById('filterJobStatus')?.value || 'ativa';
            const titleFilter = document.getElementById('filterJobTitle')?.value || '';
            
            try {
                const response = await obterVagasEmpresa(status === 'todas' ? 'todas' : status);
                
                if (response && response.success && response.data) {
                    // Filtrar por título se necessário
                    let filteredJobs = Array.isArray(response.data) ? response.data : [];
                    if (titleFilter) {
                        filteredJobs = filteredJobs.filter(job => 
                            job.titulo && job.titulo.toLowerCase().includes(titleFilter.toLowerCase())
                        );
                    }
                    jobsData = filteredJobs;
                    displayJobs();
                    setupFilters();
                } else {
                    console.error('Erro ao carregar vagas:', response);
                    const errorMsg = (response && response.error) ? response.error : 'Erro desconhecido ao carregar vagas';
                    jobsListElement.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                            <i class="fas fa-exclamation-circle text-red-600 text-2xl mb-2"></i>
                            <p class="text-red-800 font-medium">${errorMsg}</p>
                            <button onclick="loadActiveJobs()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-redo mr-2"></i>Tentar Novamente
                            </button>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erro ao carregar vagas:', error);
                jobsListElement.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                        <i class="fas fa-exclamation-circle text-red-600 text-2xl mb-2"></i>
                        <p class="text-red-800 font-medium">Erro ao carregar vagas: ${error.message || 'Erro desconhecido'}</p>
                        <p class="text-red-600 text-sm mt-2">Verifique o console para mais detalhes</p>
                        <button onclick="loadActiveJobs()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-redo mr-2"></i>Tentar Novamente
                        </button>
                    </div>
                `;
            }
        }

        function displayJobs() {
            const jobsList = document.getElementById('activeJobsList');
            
            if (jobsData.length === 0) {
                jobsList.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-briefcase text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-500 text-lg mb-2">Nenhuma vaga publicada ainda</p>
                        <p class="text-gray-400 text-sm mb-4">Comece a publicar vagas para encontrar os melhores candidatos</p>
                        <button onclick="openJobModal()" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                            <i class="fas fa-plus mr-2"></i>Publicar Primeira Vaga
                        </button>
                    </div>
                `;
                populateJobFilter();
                return;
            }

            jobsList.innerHTML = jobsData.map((job, index) => {
                const createdDate = new Date(job.created_at);
                const daysAgo = Math.floor((Date.now() - createdDate.getTime()) / (1000 * 60 * 60 * 24));
                const daysText = daysAgo === 0 ? 'Hoje' : daysAgo === 1 ? '1 dia atrás' : `${daysAgo} dias atrás`;
                
                const statusColors = {
                    'ativa': 'bg-green-100 text-green-800',
                    'pausada': 'bg-yellow-100 text-yellow-800',
                    'encerrada': 'bg-gray-100 text-gray-800'
                };
                const statusLabels = {
                    'ativa': 'Ativa',
                    'pausada': 'Pausada',
                    'encerrada': 'Encerrada'
                };
                const statusColor = statusColors[job.status] || 'bg-gray-100 text-gray-800';
                const statusLabel = statusLabels[job.status] || job.status;
                
                return `
                    <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h4 class="text-lg font-semibold text-gray-900">${job.titulo || 'Sem título'}</h4>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusColor}">
                                        ${statusLabel}
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-600">
                                    ${job.localizacao ? `<span><i class="fas fa-map-marker-alt mr-1"></i>${job.localizacao}</span>` : ''}
                                    ${job.tipo_contrato ? `<span><i class="fas fa-briefcase mr-1"></i>${job.tipo_contrato}</span>` : ''}
                                    ${job.faixa_salarial ? `<span><i class="fas fa-dollar-sign mr-1"></i>${job.faixa_salarial}</span>` : ''}
                                    <span><i class="fas fa-eye mr-1"></i>${job.visualizacoes || 0} visualizações</span>
                                </div>
                                <p class="text-sm text-gray-700 mt-3 line-clamp-2">${(job.descricao || '').substring(0, 150)}${job.descricao && job.descricao.length > 150 ? '...' : ''}</p>
                                <div class="flex items-center gap-6 mt-4 text-sm">
                                    <button onclick="loadCandidates(${job.id}); switchTab('candidatos');" class="text-purple-600 font-medium hover:text-purple-700 hover:underline flex items-center">
                                        <i class="fas fa-users mr-1"></i>${job.total_candidaturas || 0} candidato(s)
                                    </button>
                                    <span class="text-gray-500">
                                        <i class="fas fa-calendar mr-1"></i>Publicada ${daysText}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4 flex flex-col gap-2">
                                <button onclick="editJob(${job.id})" class="px-4 py-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition" title="Editar Vaga">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </button>
                                ${job.status === 'ativa' ? 
                                    `<button onclick="pauseJob(${job.id})" class="px-4 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition" title="Pausar Vaga">
                                        <i class="fas fa-pause mr-1"></i>Pausar
                                    </button>` :
                                    job.status === 'pausada' ?
                                    `<button onclick="activateJob(${job.id})" class="px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition" title="Ativar Vaga">
                                        <i class="fas fa-play mr-1"></i>Ativar
                                    </button>` : ''
                                }
                                <button onclick="deleteJob(${job.id})" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="Excluir Vaga">
                                    <i class="fas fa-trash mr-1"></i>Excluir
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            populateJobFilter();
        }
        
        // Configurar listeners de filtros após carregar vagas
        function setupFilters() {
            const filterJob = document.getElementById('filterJob');
            const filterStatus = document.getElementById('filterStatus');
            const filterNome = document.getElementById('filterNome');
            const filterEmail = document.getElementById('filterEmail');
            const filterJobStatus = document.getElementById('filterJobStatus');
            const filterJobTitle = document.getElementById('filterJobTitle');
            
            if (filterJob) {
                filterJob.removeEventListener('change', handleCandidateFilterChange);
                filterJob.addEventListener('change', handleCandidateFilterChange);
            }
            if (filterStatus) {
                filterStatus.removeEventListener('change', handleCandidateFilterChange);
                filterStatus.addEventListener('change', handleCandidateFilterChange);
            }
            if (filterNome) {
                filterNome.removeEventListener('input', handleCandidateFilterChange);
                filterNome.addEventListener('input', debounce(handleCandidateFilterChange, 500));
            }
            if (filterEmail) {
                filterEmail.removeEventListener('input', handleCandidateFilterChange);
                filterEmail.addEventListener('input', debounce(handleCandidateFilterChange, 500));
            }
            if (filterJobStatus) {
                filterJobStatus.removeEventListener('change', loadActiveJobs);
                filterJobStatus.addEventListener('change', loadActiveJobs);
            }
            if (filterJobTitle) {
                filterJobTitle.removeEventListener('input', debounce(loadActiveJobs, 500));
                filterJobTitle.addEventListener('input', debounce(loadActiveJobs, 500));
            }
        }
        
        function handleCandidateFilterChange() {
            const selectedJob = document.getElementById('filterJob').value;
            if (selectedJob) {
                loadCandidates(selectedJob);
            }
        }
        
        // Função para alternar entre tabs
        function switchTab(tab) {
            // Esconder todos os conteúdos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remover classe active de todos os botões
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'text-purple-600', 'border-purple-600');
                button.classList.add('text-gray-500', 'border-transparent');
            });
            
            // Mostrar conteúdo selecionado
            document.getElementById(`content-${tab}`).classList.remove('hidden');
            
            // Adicionar classe active ao botão selecionado
            const selectedButton = document.getElementById(`tab-${tab}`);
            selectedButton.classList.add('active', 'text-purple-600', 'border-purple-600');
            selectedButton.classList.remove('text-gray-500', 'border-transparent');
            
            // Se mudou para candidatos e tem vaga selecionada, carregar candidatos
            if (tab === 'candidatos') {
                const selectedJob = document.getElementById('filterJob').value;
                if (selectedJob) {
                    loadCandidates(selectedJob);
                }
            }
        }

        // Carregar candidatos de uma vaga
        async function loadCandidates(jobId = null) {
            const filterJob = document.getElementById('filterJob').value || jobId;
            const filterStatus = document.getElementById('filterStatus').value || 'todas';
            const filterNome = document.getElementById('filterNome').value || '';
            const filterEmail = document.getElementById('filterEmail').value || '';
            
            if (!filterJob) {
                document.getElementById('candidatesList').innerHTML = '<p class="text-gray-500 text-center py-8">Selecione uma vaga para ver os candidatos.</p>';
                return;
            }
            
            try {
                const params = new URLSearchParams({
                    job_id: filterJob,
                    status: filterStatus,
                    nome: filterNome,
                    email: filterEmail
                });
                
                const response = await fetch(`api/candidaturas_vaga.php?${params}`);
                const result = await response.json();
                
                if (result.success) {
                    displayCandidates(result.data);
                } else {
                    document.getElementById('candidatesList').innerHTML = `<p class="text-red-500 text-center py-8">${result.error || 'Erro ao carregar candidatos'}</p>`;
                }
            } catch (error) {
                console.error('Erro ao carregar candidatos:', error);
                document.getElementById('candidatesList').innerHTML = '<p class="text-red-500 text-center py-8">Erro ao carregar candidatos.</p>';
            }
        }
        
        function displayCandidates(candidates) {
            const candidatesList = document.getElementById('candidatesList');
            
            if (candidates.length === 0) {
                candidatesList.innerHTML = '<p class="text-gray-500 text-center py-8">Nenhum candidato encontrado.</p>';
                return;
            }
            
            const statusColors = {
                'pendente': 'bg-yellow-100 text-yellow-800',
                'em_analise': 'bg-blue-100 text-blue-800',
                'aprovada': 'bg-green-100 text-green-800',
                'rejeitada': 'bg-red-100 text-red-800',
                'cancelada': 'bg-gray-100 text-gray-800'
            };
            
            const statusLabels = {
                'pendente': 'Pendente',
                'em_analise': 'Em Análise',
                'aprovada': 'Aprovada',
                'rejeitada': 'Reprovada',
                'cancelada': 'Cancelada'
            };
            
            candidatesList.innerHTML = candidates.map(candidate => {
                const statusColor = statusColors[candidate.status] || 'bg-gray-100 text-gray-800';
                const statusLabel = statusLabels[candidate.status] || candidate.status;
                const createdDate = new Date(candidate.created_at);
                const dateStr = createdDate.toLocaleDateString('pt-BR');
                
                let starsHtml = '';
                if (candidate.avaliacao) {
                    starsHtml = '<div class="flex items-center mt-2">';
                    for (let i = 1; i <= 5; i++) {
                        starsHtml += `<i class="fas fa-star ${i <= candidate.avaliacao ? 'text-yellow-400' : 'text-gray-300'}"></i>`;
                    }
                    starsHtml += '</div>';
                }
                
                return `
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    ${candidate.foto_perfil ? 
                                        `<img src="${candidate.foto_perfil}" alt="${candidate.candidato_nome}" class="w-12 h-12 rounded-full object-cover">` :
                                        `<div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600"></i>
                                        </div>`
                                    }
                                    <div>
                                        <h4 class="font-semibold text-gray-900">${candidate.candidato_nome || 'Sem nome'}</h4>
                                        <p class="text-sm text-gray-600">${candidate.candidato_email || ''}</p>
                                    </div>
                                </div>
                                <div class="ml-15 space-y-1">
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-briefcase mr-1"></i>${candidate.vaga_titulo || 'Vaga'}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-calendar mr-1"></i>Candidatou-se em ${dateStr}
                                    </p>
                                    ${candidate.tipo_deficiencia ? 
                                        `<p class="text-sm text-gray-600">
                                            <i class="fas fa-wheelchair mr-1"></i>${candidate.tipo_deficiencia}
                                        </p>` : ''
                                    }
                                    ${candidate.mensagem ? 
                                        `<p class="text-sm text-gray-700 mt-2 p-2 bg-gray-50 rounded">
                                            <strong>Mensagem:</strong> ${candidate.mensagem.substring(0, 150)}${candidate.mensagem.length > 150 ? '...' : ''}
                                        </p>` : ''
                                    }
                                    ${candidate.feedback ? 
                                        `<p class="text-sm text-red-700 mt-2 p-2 bg-red-50 rounded">
                                            <strong>Feedback:</strong> ${candidate.feedback}
                                        </p>` : ''
                                    }
                                    ${starsHtml}
                                </div>
                            </div>
                            <div class="ml-4 flex flex-col items-end gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColor}">
                                    ${statusLabel}
                                </span>
                                <div class="flex gap-2 mt-2">
                                    <button onclick="openManageCandidateModal(${candidate.id}, '${candidate.status}', '${(candidate.feedback || '').replace(/'/g, "\\'")}', ${candidate.avaliacao || 'null'})" 
                                            class="text-purple-600 hover:text-purple-700 p-2" title="Gerenciar">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    ${candidate.curriculo_path ? 
                                        `<a href="${candidate.curriculo_path}" target="_blank" class="text-blue-600 hover:text-blue-700 p-2" title="Ver Currículo">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>` : ''
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Popular select de vagas
        function populateJobFilter() {
            const filterJob = document.getElementById('filterJob');
            if (!filterJob) return;
            
            filterJob.innerHTML = '<option value="">Selecione uma vaga</option>';
            
            jobsData.forEach(job => {
                const option = document.createElement('option');
                option.value = job.id;
                const statusLabel = job.status === 'ativa' ? ' (Ativa)' : job.status === 'pausada' ? ' (Pausada)' : ' (Encerrada)';
                option.textContent = job.titulo + statusLabel;
                filterJob.appendChild(option);
            });
        }
        
        // Abrir modal de gerenciamento
        function openManageCandidateModal(applicationId, currentStatus, currentFeedback, currentAvaliacao) {
            document.getElementById('manageCandidateModal').classList.remove('hidden');
            document.getElementById('manageApplicationId').value = applicationId;
            document.getElementById('manageStatus').value = currentStatus;
            document.getElementById('manageFeedback').value = currentFeedback || '';
            document.getElementById('manageAvaliacao').value = currentAvaliacao || '';
            
            // Atualizar campo obrigatório
            const statusSelect = document.getElementById('manageStatus');
            const feedbackRequired = document.getElementById('feedbackRequired');
            const feedbackTextarea = document.getElementById('manageFeedback');
            
            function updateFeedbackRequired() {
                if (statusSelect.value === 'rejeitada') {
                    feedbackRequired.style.display = 'inline';
                    feedbackTextarea.required = true;
                } else {
                    feedbackRequired.style.display = 'none';
                    feedbackTextarea.required = false;
                }
            }
            
            updateFeedbackRequired();
            
            // Remover listener anterior se existir e adicionar novo
            statusSelect.removeEventListener('change', updateFeedbackRequired);
            statusSelect.addEventListener('change', updateFeedbackRequired);
        }
        
        function closeManageCandidateModal() {
            document.getElementById('manageCandidateModal').classList.add('hidden');
        }
        
        async function saveCandidateStatus() {
            const applicationId = document.getElementById('manageApplicationId').value;
            const status = document.getElementById('manageStatus').value;
            const feedback = document.getElementById('manageFeedback').value;
            const avaliacao = document.getElementById('manageAvaliacao').value || null;
            
            if (status === 'rejeitada' && !feedback.trim()) {
                alert('Feedback é obrigatório ao reprovar candidatura.');
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('application_id', applicationId);
                formData.append('status', status);
                formData.append('feedback', feedback);
                if (avaliacao) {
                    formData.append('avaliacao', avaliacao);
                }
                
                const response = await fetch('api/atualizar_status_candidatura.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erro HTTP:', response.status, errorText);
                    try {
                        const errorJson = JSON.parse(errorText);
                        alert('Erro: ' + (errorJson.error || 'Erro ao processar requisição'));
                    } catch (e) {
                        alert('Erro: Erro ao processar requisição (Status ' + response.status + ')');
                    }
                    return;
                }
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Status atualizado com sucesso!');
                    closeManageCandidateModal();
                    loadCandidates();
                } else {
                    alert('Erro: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao atualizar status:', error);
                alert('Erro ao atualizar status: ' + error.message);
            }
        }
        
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Modal functions
        function openPlansModal() {
            document.getElementById('plansModal').classList.remove('hidden');
        }

        function closePlansModal() {
            document.getElementById('plansModal').classList.add('hidden');
        }

        function openUploadLogoModal() {
            document.getElementById('uploadLogoModal').classList.remove('hidden');
        }

        function closeUploadLogoModal() {
            document.getElementById('uploadLogoModal').classList.add('hidden');
            document.getElementById('uploadLogoForm').reset();
            document.getElementById('logoPreview').classList.add('hidden');
        }

        // Preview da imagem antes de enviar
        document.getElementById('logoFile')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logoPreviewImg').src = e.target.result;
                    document.getElementById('logoPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('logoPreview').classList.add('hidden');
            }
        });

        // Upload do logo
        document.getElementById('uploadLogoForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('logoFile');
            if (!fileInput.files.length) {
                alert('Selecione uma imagem');
                return;
            }
            
            const formData = new FormData();
            formData.append('logo', fileInput.files[0]);
            
            try {
                const response = await fetch('api/upload_logo_empresa.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Logo atualizado com sucesso!');
                    closeUploadLogoModal();
                    // Recarregar dados da empresa para atualizar o logo
                    await loadCompanyData();
                } else {
                    alert('Erro ao atualizar logo: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao fazer upload do logo:', error);
                alert('Erro ao fazer upload do logo. Tente novamente.');
            }
        });

        function openEditCompanyModal() {
            if (companyData) {
                document.getElementById('editCompanyName').value = companyData.nome_fantasia || companyData.razao_social || '';
                document.getElementById('editCompanyIndustry').value = companyData.setor || '';
                const location = [];
                if (companyData.cidade) location.push(companyData.cidade);
                if (companyData.estado) location.push(companyData.estado);
                document.getElementById('editCompanyLocation').value = location.join(', ');
                document.getElementById('editCompanyAbout').value = companyData.descricao || '';
            }
            document.getElementById('editCompanyModal').classList.remove('hidden');
        }

        function closeEditCompanyModal() {
            document.getElementById('editCompanyModal').classList.add('hidden');
        }

        function openJobModal() {
            document.getElementById('jobModal').classList.remove('hidden');
        }

        function closeJobModal() {
            document.getElementById('jobModal').classList.add('hidden');
            resetJobForm();
        }

        // Form submissions
        document.getElementById('editCompanyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Redirecionar para processar_perfil_empresa.php
            // Por enquanto, apenas atualizar visualmente
            document.getElementById('companyName').textContent = document.getElementById('editCompanyName').value;
            document.getElementById('companyIndustry').textContent = document.getElementById('editCompanyIndustry').value;
            document.getElementById('companyLocation').textContent = document.getElementById('editCompanyLocation').value;
            document.getElementById('companyAbout').textContent = document.getElementById('editCompanyAbout').value;
            
            closeEditCompanyModal();
            alert('Para salvar as alterações, será necessário implementar o formulário completo de edição.');
        });

        document.getElementById('newJobForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Debug: Verificar se requisitos está sendo capturado
            console.log('Requisitos capturado:', formData.get('requisitos'));
            
            // Se não tem job_id, é criação
            if (!formData.get('job_id')) {
                formData.append('action', 'create');
            }
            
            // Garantir que todos os campos estão sendo enviados (mesmo se vazio)
            const requisitos = document.getElementById('jobRequirements').value || '';
            formData.set('requisitos', requisitos);
            
            // Debug: Log de todos os campos
            console.log('Dados do formulário:', {
                titulo: formData.get('titulo'),
                descricao: formData.get('descricao'),
                requisitos: formData.get('requisitos'),
                localizacao: formData.get('localizacao'),
                tipo_contrato: formData.get('tipo_contrato')
            });
            
            try {
                const response = await fetch('processar_vaga.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erro HTTP:', response.status, errorText);
                    throw new Error(`Erro ${response.status}: ${errorText.substring(0, 200)}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message || 'Vaga salva com sucesso!');
                    closeJobModal();
                    loadActiveJobs();
                    loadCompanyData(); // Recarregar estatísticas
                } else {
                    const errors = result.errors || ['Erro ao salvar vaga'];
                    console.error('Erros:', errors);
                    alert('Erros encontrados:\n' + errors.join('\n'));
                }
            } catch (error) {
                console.error('Erro ao salvar vaga:', error);
                alert('Erro ao salvar vaga: ' + error.message);
            }
        });

        async function subscribePlan(planName, price) {
            if (confirm(`Deseja solicitar o Plano ${planName} por R$${price}/mês?\n\nSua solicitação será enviada para aprovação do administrador.`)) {
                try {
                    // Converter nome do plano para formato do banco
                    const planMap = {
                        'Essencial': 'essencial',
                        'Profissional': 'profissional',
                        'Enterprise': 'enterprise'
                    };
                    
                    const planoSlug = planMap[planName] || planName.toLowerCase();
                    
                    console.log('Solicitando plano:', { plano: planoSlug, valor: price });
                    
                    // Verificar se a função existe
                    if (!window.ViggedAPI || !window.ViggedAPI.solicitarPlano) {
                        alert('Erro: Função de solicitar plano não encontrada. Recarregue a página.');
                        console.error('ViggedAPI.solicitarPlano não encontrada');
                        return;
                    }
                    
                    // Enviar solicitação
                    const result = await ViggedAPI.solicitarPlano(planoSlug, price);
                    
                    console.log('Resultado da solicitação:', result);
                    
                    if (result.success) {
                        closePlansModal();
                        alert(`Solicitação do Plano ${planName} enviada com sucesso!\n\nAguarde a aprovação do administrador. Você será notificado quando o plano for aprovado.`);
                        
                        // Recarregar dados da empresa para atualizar status
                        if (typeof loadCompanyData === 'function') {
                            loadCompanyData();
                        } else {
                            location.reload();
                        }
                    } else {
                        const errorMsg = result.error || 'Erro desconhecido';
                        const details = result.details ? '\n\nDetalhes: ' + result.details : '';
                        alert('Erro ao solicitar plano: ' + errorMsg + details);
                        console.error('Erro detalhado:', result);
                    }
                } catch (error) {
                    console.error('Erro ao solicitar plano:', error);
                    alert('Erro ao solicitar plano: ' + (error.message || 'Erro desconhecido') + '\n\nVerifique o console para mais detalhes.');
                }
            }
        }

        async function editJob(jobId) {
            const job = jobsData.find(j => j.id === jobId);
            if (!job) {
                alert('Vaga não encontrada');
                return;
            }
            
            // Preencher formulário com dados da vaga
            document.getElementById('jobTitle').value = job.titulo || '';
            document.getElementById('jobLocation').value = job.localizacao || '';
            document.getElementById('jobType').value = job.tipo_contrato || 'CLT';
            document.getElementById('jobSalary').value = job.faixa_salarial || '';
            document.getElementById('jobDescription').value = job.descricao || '';
            document.getElementById('jobRequirements').value = job.requisitos || '';
            
            // Adicionar campo hidden com job_id para edição
            let jobIdInput = document.getElementById('jobId');
            if (!jobIdInput) {
                jobIdInput = document.createElement('input');
                jobIdInput.type = 'hidden';
                jobIdInput.id = 'jobId';
                jobIdInput.name = 'job_id';
                document.getElementById('newJobForm').appendChild(jobIdInput);
            }
            jobIdInput.value = jobId;
            
            // Adicionar campo hidden com action = update
            let actionInput = document.getElementById('actionInput');
            if (!actionInput) {
                actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.id = 'actionInput';
                actionInput.name = 'action';
                document.getElementById('newJobForm').appendChild(actionInput);
            }
            actionInput.value = 'update';
            
            // Atualizar título do modal
            document.querySelector('#jobModal h3').textContent = 'Editar Vaga';
            document.querySelector('#jobModal button[type="submit"]').textContent = 'Salvar Alterações';
            
            openJobModal();
        }
        
        function resetJobForm() {
            document.getElementById('jobTitle').value = '';
            document.getElementById('jobLocation').value = '';
            document.getElementById('jobType').value = 'CLT';
            document.getElementById('jobSalary').value = '';
            document.getElementById('jobDescription').value = '';
            document.getElementById('jobRequirements').value = '';
            
            const jobIdInput = document.getElementById('jobId');
            if (jobIdInput) jobIdInput.remove();
            
            const actionInput = document.getElementById('actionInput');
            if (actionInput) actionInput.remove();
            
            document.querySelector('#jobModal h3').textContent = 'Publicar Nova Vaga';
            document.querySelector('#jobModal button[type="submit"]').textContent = 'Publicar Vaga';
        }

        async function deleteJob(jobId) {
            if (!confirm('Deseja realmente excluir esta vaga? Esta ação não pode ser desfeita.')) {
                return;
            }
            
            try {
                const response = await gerenciarVaga(jobId, 'deletar');
                if (response.success) {
                    alert('Vaga excluída com sucesso!');
                    loadActiveJobs();
                } else {
                    alert('Erro ao excluir vaga: ' + (response.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao excluir vaga:', error);
                alert('Erro ao excluir vaga. Tente novamente.');
            }
        }
        
        async function pauseJob(jobId) {
            if (!confirm('Deseja pausar esta vaga? Ela não aparecerá mais nas buscas até ser reativada.')) {
                return;
            }
            
            try {
                const response = await gerenciarVaga(jobId, 'pausar');
                if (response.success) {
                    alert('Vaga pausada com sucesso!');
                    loadActiveJobs();
                } else {
                    alert('Erro ao pausar vaga: ' + (response.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao pausar vaga:', error);
                alert('Erro ao pausar vaga. Tente novamente.');
            }
        }
        
        async function activateJob(jobId) {
            try {
                const response = await gerenciarVaga(jobId, 'ativar');
                if (response.success) {
                    alert('Vaga ativada com sucesso!');
                    loadActiveJobs();
                } else {
                    alert('Erro ao ativar vaga: ' + (response.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao ativar vaga:', error);
                alert('Erro ao ativar vaga. Tente novamente.');
            }
        }

        function logout() {
            if (confirm('Deseja realmente sair?')) {
                window.location.href = 'index.php';
            }
        }

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado, iniciando carregamento de dados...');
            switchTab('vagas');
            
            // Aguardar um pouco para garantir que tudo está pronto
            setTimeout(() => {
                loadCompanyData();
            }, 100);
        });
        
        // Também tentar carregar imediatamente se o DOM já estiver pronto
        if (document.readyState === 'loading') {
            // DOM ainda não está pronto, aguardar evento
        } else {
            // DOM já está pronto
            console.log('DOM já pronto, carregando dados imediatamente...');
            setTimeout(() => {
                loadCompanyData();
            }, 100);
        }
        
        // Funções para menu de configurações
        function toggleAccountSettings() {
            const menu = document.getElementById('accountSettingsMenu');
            const chevron = document.getElementById('settingsChevron');
            menu.classList.toggle('hidden');
            chevron.classList.toggle('fa-chevron-down');
            chevron.classList.toggle('fa-chevron-up');
        }
        
        // Fechar menu ao clicar fora
        document.addEventListener('click', function(event) {
            const settingsButton = event.target.closest('[onclick="toggleAccountSettings()"]');
            const settingsMenu = document.getElementById('accountSettingsMenu');
            if (settingsMenu && !settingsButton && !settingsMenu.contains(event.target)) {
                settingsMenu.classList.add('hidden');
                const chevron = document.getElementById('settingsChevron');
                if (chevron) {
                    chevron.classList.remove('fa-chevron-up');
                    chevron.classList.add('fa-chevron-down');
                }
            }
        });
        
        // Funções para modais de configurações
        function openChangePasswordModal() {
            alert('Funcionalidade de trocar senha será implementada em breve.');
            toggleAccountSettings();
        }
        
        function openChangeEmailModal() {
            alert('Funcionalidade de trocar email será implementada em breve.');
            toggleAccountSettings();
        }
        
        // Funções para excluir conta
        function openDeleteAccountModal() {
            document.getElementById('deleteAccountModal').classList.remove('hidden');
        }
        
        function closeDeleteAccountModal() {
            document.getElementById('deleteAccountModal').classList.add('hidden');
            document.getElementById('confirmDeleteInput').value = '';
        }
        
        async function confirmDeleteAccount() {
            const confirmInput = document.getElementById('confirmDeleteInput');
            if (confirmInput.value.toLowerCase() !== 'excluir') {
                alert('Por favor, digite "EXCLUIR" para confirmar a exclusão da conta.');
                return;
            }
            
            // Verificar se ViggedAPI está disponível
            if (!window.ViggedAPI || !window.ViggedAPI.excluirConta) {
                console.error('ViggedAPI não está disponível. Verifique se o arquivo api.js foi carregado.');
                alert('Erro: API não disponível. Por favor, recarregue a página e tente novamente.');
                return;
            }
            
            const deleteButton = document.getElementById('confirmDeleteButton');
            const originalText = deleteButton.innerHTML;
            deleteButton.disabled = true;
            deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Excluindo...';
            
            try {
                const result = await ViggedAPI.excluirConta();
                
                if (result.success) {
                    alert('Sua conta foi excluída com sucesso. Você será redirecionado para a página inicial.');
                    window.location.href = 'index.php';
                } else {
                    const errorMsg = result.error || 'Erro desconhecido';
                    console.error('Erro ao excluir conta:', result);
                    alert('Erro ao excluir conta: ' + errorMsg + (result.debug ? '\n\nDetalhes: ' + result.debug : ''));
                    deleteButton.disabled = false;
                    deleteButton.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Erro ao excluir conta:', error);
                alert('Erro ao excluir conta: ' + (error.message || 'Tente novamente mais tarde.'));
                deleteButton.disabled = false;
                deleteButton.innerHTML = originalText;
            }
        }
    </script>
