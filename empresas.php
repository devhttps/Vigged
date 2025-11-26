<?php
// Configurar título da página
$title = 'Empresas';

// Iniciar sessão para manter autenticação
require_once 'config/auth.php';
startSecureSession();

// Estilos customizados para esta página
$additionalStyles = [
    '<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        .popular-badge {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 0.25rem 1rem;
            border-radius: 9999px;
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .plan-card {
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(124, 58, 237, 0.2);
        }

        .benefit-icon {
            width: 60px;
            height: 60px;
            background: #7c3aed;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
    </style>'
];

// Incluir head
include 'includes/head.php';

// Incluir navegação pública
// Incluir navegação (será determinada automaticamente pela autenticação)
include 'includes/nav.php';
?>
    
    <!-- Hero Section -->
    <section class="bg-white py-16 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl font-bold text-purple-600 mb-6">Admita mais PCDs</h1>
            <p class="text-xl text-gray-700 mb-8 max-w-3xl mx-auto">
                Oferecemos planos personalizados para empresas divulgarem suas vagas inclusivas e encontrarem talentos PCD.
            </p>
            <div class="flex justify-center gap-4 flex-wrap">
                <a href="cadastro-empresa.php" class="bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition text-lg">
                    Publicar Vagas
                </a>
                <a href="cadastro-empresa.php" class="bg-white text-purple-600 border-2 border-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-purple-50 transition text-lg">
                    Conhecer Planos
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Vigged Section -->
    <section class="bg-gray-50 py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-bold text-purple-600 text-center mb-12">Por que escolher a Vigged?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Benefit 1 -->
                <div class="bg-white p-8 rounded-xl text-center">
                    <div class="benefit-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Banco de Talentos PCD</h3>
                    <p class="text-gray-600">
                        Acesse os candidatos qualificados e escritos para trabalhar com sua empresa.
                    </p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white p-8 rounded-xl text-center">
                    <div class="benefit-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Relatórios Detalhados</h3>
                    <p class="text-gray-600">
                        Acompanhe o desempenho das suas vagas e otimize suas ações recrutadas.
                    </p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white p-8 rounded-xl text-center">
                    <div class="benefit-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Consultoria Especializada</h3>
                    <p class="text-gray-600">
                        Suporte mais com um consultoria de trabalho muito inclusiva e acessível.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Plans Section -->
    <section class="bg-gradient-to-br from-purple-600 to-purple-800 py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-bold text-white text-center mb-12">Nossos Planos</h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Plan 1 - Essencial -->
                <div class="plan-card bg-white rounded-2xl p-8 shadow-xl">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2 text-center">Plano Essencial - Inclusão Consciente</h3>
                    <div class="text-center mb-6">
                        <span class="text-5xl font-bold text-purple-600">R$199</span>
                        <span class="text-gray-600">/mês</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">3 vagas ativas por mês</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Divulgação vagas até 30 dias</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Vagas destacadas em até 5 dias incluídas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Acesso a dados e relatórios sobre candidaturas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Suporte técnico por email</span>
                        </li>
                    </ul>
                    <a href="cadastro-empresa.php" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Contratar Plano
                    </a>
                </div>

                <!-- Plan 2 - Profissional (Popular) -->
                <div class="plan-card bg-white rounded-2xl p-8 shadow-xl relative">
                    <div class="popular-badge">MAIS POPULAR</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2 text-center mt-4">Plano Profissional - Diversidade em Foco</h3>
                    <div class="text-center mb-6">
                        <span class="text-5xl font-bold text-purple-600">R$399</span>
                        <span class="text-gray-600">/mês</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">10 vagas ativas por mês</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Divulgação vagas até 60 dias</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Vagas em destaque na página principal</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Envio automático para vagas banco de talentos</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Relatórios mensais de desempenho das vagas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Suporte por email e WhatsApp</span>
                        </li>
                    </ul>
                    <a href="cadastro-empresa.php" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Contratar Plano
                    </a>
                </div>

                <!-- Plan 3 - Enterprise -->
                <div class="plan-card bg-white rounded-2xl p-8 shadow-xl">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2 text-center">Plano Enterprise - Inclusão Total</h3>
                    <div class="text-center mb-6">
                        <span class="text-5xl font-bold text-purple-600">R$799</span>
                        <span class="text-gray-600">/mês</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Vagas ilimitadas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Divulgação ilimitada (vagas ativas por até 90 dias)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Destaque premium com logomarca da empresa</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Acesso a consultoria de inclusão e Diversidade</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Relatórios avançados e segmentados</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Suporte prioritário (WhatsApp e telefone direto)</span>
                        </li>
                    </ul>
                    <a href="cadastro-empresa.php" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Contratar Plano
                    </a>
                </div>
            </div>
        </div>
    </section>
<?php
// Incluir footer padrão
include 'includes/footer.php';
?>
